<?php
namespace Df\Payment;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OrderPayment;
use Magento\Payment\Model\Info;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver as AssignObserver;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment as QuotePayment;
abstract class Method implements MethodInterface {
	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's acceptPayment() used? https://mage2.pro/t/715
	 *
	 * @see \Magento\Payment\Model\MethodInterface::acceptPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L304-L312
	 * @see \Magento\Payment\Model\Method\AbstractMethod::acceptPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L696-L713
	 *
	 * @param InfoInterface $payment
	 * @return bool
	 */
	public function acceptPayment(InfoInterface $payment) {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's assignData() used? https://mage2.pro/t/718
	 *
	 * @see \Magento\Payment\Model\MethodInterface::assignData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L304-L312
	 * @see \Magento\Payment\Model\Method\AbstractMethod::assignData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L696-L713
	 *
	 * ISSUES with @see \Magento\Payment\Model\Method\AbstractMethod::assignData():
	 * 1) The @see \Magento\Payment\Model\Method\AbstractMethod::assignData() method
	 * can be simplified: https://mage2.pro/t/719
	 * 2) The @see \Magento\Payment\Model\Method\AbstractMethod::assignData() method
	 * has a wrong PHPDoc declaration: https://mage2.pro/t/720
	 *
	 * @param DataObject $data
	 * @return bool
	 */
	public function assignData(DataObject $data) {
		$this->getInfoInstance()->addData($data->getData());
		$eventParams = [
			AssignObserver::METHOD_CODE => $this,
			AssignObserver::MODEL_CODE => $this->getInfoInstance(),
			AssignObserver::DATA_CODE => $data
		];
		df_dispatch('payment_method_assign_data_' . $this->getCode(), $eventParams);
		df_dispatch('payment_method_assign_data', $eventParams);
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's authorize() used? https://mage2.pro/t/707
	 *
	 * @see \Magento\Payment\Model\MethodInterface::authorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L249-L257
	 * @see \Magento\Payment\Model\Method\AbstractMethod::authorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L603-L619
	 * @param InfoInterface $payment
	 * @param float $amount
	 * @return $this
	 */
	public function authorize(InfoInterface $payment, $amount) {return $this;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/t/644
	 * The method canAuthorize() should be removed from the interface
	 * @see \Magento\Payment\Model\MethodInterface,
	 * because it is used only by a particular interface's implementation
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 * and by vault payment methods.
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canAuthorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L63-L69
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canAuthorize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L297-L306
	 * @return bool
	 */
	public function canAuthorize() {df_should_not_be_here(__METHOD__);}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/tags/capture
	 *
	 * Важно для витрины вернуть true, чтобы
	 * @see Df_Payment_Model_Action_Confirm::process() и другие аналогичные методы
	 * (например, @see Df_Alfabank_Model_Action_CustomerReturn::process())
	 * могли вызвать @see Mage_Sales_Model_Order_Invoice::capture().
	 *
	 * Для административной части возвращайте true только в том случае,
	 * если метод оплаты реально поддерживает операцию capture
	 * (т.е. имеет класс Df_XXX_Model_Request_Capture).
	 * Реализация этого класса позволит проводить двуступенчатую оплату:
	 * резервирование средств непосредственно в процессе оформления заказа
	 * и снятие средств посредством нажатия кнопки «Принять оплату» («Capture»)
	 * на административной странице счёта.
	 *
	 * Обратите внимание, что двуступенчатая оплата
	 * имеет смысл не только для дочернего данному класса @see Df_Payment_Model_Method_WithRedirect,
	 * но и для других прямых детей класса @see Df_Payment_Model_Method.
	 * @todo Например, правильным будет сделать оплату двуступенчатой для модуля «Квитанция Сбербанка»,
	 * потому что непосредственно по завершению заказа
	 * неправильно переводить счёт в состояние «Оплачен»
	 * (ведь он не оплачен! покупатель получил просто ссылку на квитанцию и далеко неочевидно,
	 * что он оплатит эту квитанцию).
	 * Вместо этого правильно будет оставлять счёт в открытом состоянии
	 * и переводить его в оплаченное состояние только после оплаты.
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L71-L77
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L308-L317
	 *
	 * USAGES
	 * How is payment method's canCapture() used?
	 * https://mage2.pro/t/645
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canCapture() used?
	 * https://mage2.pro/t/650
	 *
	 * @used-by \Magento\Payment\Model\Method\AbstractMethod::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L631-L638
	 *
	 * @used-by \Magento\Vault\Model\Method\Vault::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Vault/Model/Method/Vault.php#L222-L226
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment::canCapture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment.php#L263-L267
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment::_invoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment.php#L532-L534
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation::invoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment/Operations/AbstractOperation.php#L69-L71
	 *
	 * @return bool
	 */
	public function canCapture() {return !df_is_admin();}

	/**
	 * 2016-02-10
	 * @override
	 * https://mage2.pro/tags/capture
	 *
	 * https://mage2.pro/t/658
	 * The @see \Magento\Payment\Model\MethodInterface::canCaptureOnce() is never used
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canCaptureOnce()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L87-L93
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCaptureOnce()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L330-L339
	 *
	 * @return bool
	 */
	public function canCaptureOnce() {return false;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/tags/capture
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canCapturePartial()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L79-L85
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canCapturePartial()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L325-L328
	 *
	 * USAGES
	 * How is payment method's canCapturePartial() used?
	 * https://mage2.pro/t/648
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canCapturePartial() used?
	 * https://mage2.pro/t/649
	 *
	 * @used-by \Magento\Sales\Model\Order\Payment::canCapturePartial()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Sales/Model/Order/Payment.php#L302-L305
	 *
	 * @return bool
	 */
	public function canCapturePartial() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's cancel() used? https://mage2.pro/t/710
	 *
	 * @see \Magento\Payment\Model\MethodInterface::cancel()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L279-L286
	 * @see \Magento\Payment\Model\Method\AbstractMethod::cancel()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L658-L669
	 * @param InfoInterface $payment
	 * @return $this
	 */
	public function cancel(InfoInterface $payment) {return $this;}

	/**
	 * 2016-02-10
	 * @override
	 * How is a payment method's canEdit() used? https://mage2.pro/t/672
	 * How is @see \Magento\Sales\Model\Order::canEdit() implemented and used? https://mage2.pro/t/673
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canEdit()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L133-L139
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canEdit()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L395-L404
	 * @return bool
	 */
	public function canEdit() {return true;}

	/**
	 * 2016-02-11
	 * @override
	 * https://mage2.pro/tags/payment-transaction
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canFetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L141-L147
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canFetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L406-L415
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/676
	 * How is a payment method's canFetchTransactionInfo() used?
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canFetchTransactionInfo() implemented and used?
	 * https://mage2.pro/t/677
	 */
	public function canFetchTransactionInfo() {return false;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/t/640
	 * The method canOrder() should be removed from the interface
	 * @see \Magento\Payment\Model\MethodInterface,
	 * because it is not used outside of a particular interface's implementation
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canOrder()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L55-L61
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canOrder()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L286-L295
	 * @return bool
	 */
	public function canOrder() {df_should_not_be_here(__METHOD__);}

	/**
	 * 2016-02-10
	 * @override
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю.
	 * https://mage2.pro/tags/refund
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canRefund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L95-L101
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canRefund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L341-L350
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/659
	 * How is a payment method's canRefund() used?
	 */
	public function canRefund() {return false;}

	/**
	 * 2016-02-10
	 * @override
	 * https://mage2.pro/tags/refund
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canRefundPartialPerInvoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L103-L109
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canRefundPartialPerInvoice()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L352-L361
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/663
	 * How is a payment method's canRefundPartialPerInvoice() used?
	 */
	public function canRefundPartialPerInvoice() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's canReviewPayment() used? https://mage2.pro/t/714
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canReviewPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L297-L302
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canReviewPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L688-L696
	 * @return bool
	 */
	public function canReviewPayment() {return false;}

	/**
	 * 2016-02-10
	 * @override
	 * The same as @see \Df\Payment\Method::canUseInternal(), but it is used for the frontend only.
	 * https://mage2.pro/t/671
	 * https://mage2.pro/tags/payment-can-use
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseCheckout()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L126-L131
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseCheckout()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L156-L161
	 * @return bool
	 */
	public function canUseCheckout() {return true;}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's canUseForCountry() used? https://mage2.pro/t/682
	 * The method @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCountry()
	 * can be simplified: https://mage2.pro/t/683
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseForCountry()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L184-L190
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCountry()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L464-L482
	 * @param string $country
	 * @return bool
	 */
	public function canUseForCountry($country) {
		return !$this->getConfigData('allowspecific')
		   || in_array($country, df_csv_parse($this->getConfigData('specificcountry')))
		;
	}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's canUseForCurrency() used? https://mage2.pro/t/684
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseForCurrency()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L192-L199
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseForCurrency()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L484-L494
	 * @param string $currencyCode
	 * @return bool
	 */
	public function canUseForCurrency($currencyCode) {return true;}

	/**
	 * 2016-02-10
	 * @override
	 * Place in your custom canUseInternal() method a custom logic to decide
	 * whether the payment method need to be shown to a customer on the checkout screen.
	 * By default there is no custom login and the method just returns true.
	 * https://mage2.pro/t/670
	 * https://mage2.pro/tags/payment-can-use
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canUseInternal()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L118-L124
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canUseInternal()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L149-L154
	 * @return bool
	 */
	public function canUseInternal() {return true;}

	/**
	 * 2016-02-10
	 * @override
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированное разблокирование (возврат покупателю)
	 * ранее зарезервированных (но не снятых со счёта покупателя) средств
	 * https://mage2.pro/tags/void
	 *
	 * @see \Magento\Payment\Model\MethodInterface::canVoid()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L111-L116
	 * @see \Magento\Payment\Model\Method\AbstractMethod::canVoid()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L363-L372
	 * @return bool
	 *
	 * USAGES
	 * https://mage2.pro/t/666
	 * How is a payment method's canVoid() used?
	 *
	 * How is @see \Magento\Sales\Model\Order\Payment::canVoid() implemented and used?
	 * https://mage2.pro/t/667
	 */
	public function canVoid() {return false;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's capture() used? https://mage2.pro/t/708
	 *
	 * @see \Magento\Payment\Model\MethodInterface::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L259-L267
	 * @see \Magento\Payment\Model\Method\AbstractMethod::capture()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L621-L638
	 * @param InfoInterface $payment
	 * @param float $amount
	 * @return $this
	 */
	public function capture(InfoInterface $payment, $amount) {return $this;}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's denyPayment() used? https://mage2.pro/t/716
	 *
	 * @see \Magento\Payment\Model\MethodInterface::denyPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L314-L322
	 * @see \Magento\Payment\Model\Method\AbstractMethod::denyPayment()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L715-L730
	 *
	 * @param InfoInterface $payment
	 * @return bool
	 */
	public function denyPayment(InfoInterface $payment) {return false;}

	/**
	 * 2016-02-11
	 * @override
	 *
	 * @see \Magento\Payment\Model\MethodInterface::fetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L149-L158
	 * @see \Magento\Payment\Model\Method\AbstractMethod::fetchTransactionInfo()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L417-L428
	 *
	 * @param InfoInterface $payment
	 * @param string $transactionId
	 * @return array(string => mixed)
	 *
	 * USAGES
	 * https://mage2.pro/t/678
	 * How is a payment method's fetchTransactionInfo() used?
	 */
	public function fetchTransactionInfo(InfoInterface $payment, $transactionId) {return [];}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L17-L23
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getCode()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L496-L508
	 * @return string
	 */
	public function getCode() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2016-02-16
			 * @see \Dfe\Stripe\Method => «dfe_stripe»
			 */
			$this->{__METHOD__} = df_cts_lc(str_replace('\\Method', '', df_cts($this)), '_');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's getConfigData() used? https://mage2.pro/t/717
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getConfigData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L324-L332
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getConfigData()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L742-L760
	 * @param string $field
	 * @param null|string|int|ScopeInterface $storeId [optional]
	 * @return string|null
	 */
	public function getConfigData($field, $storeId = null) {
		static $map = [
			/**
			 * 2016-02-16
			 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Config.php#L85-L105
			 * @uses \Df\Payment\Method::isActive()
			 */
			'active' => 'isActive'
			/**
			 * 2016-02-16
			 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Helper/Data.php#L265-L274
			 * @uses \Df\Payment\Method::getTitle()
			 */
			,'title' => 'getTitle'
		];
		return
			isset($map[$field])
			? call_user_func([$this, $map[$field]], $storeId)
			: $this->settings($field, $storeId)
		;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's getConfigPaymentAction() used? https://mage2.pro/t/724
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getConfigPaymentAction()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L374-L381
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getConfigPaymentAction()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L854-L864
	 *
	 * @return $this
	 */
	public function getConfigPaymentAction() {return $this->getConfigData('payment_action');}

	/**
	 * 2016-02-08
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::getFormBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L25-L32
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getFormBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L510-L518
	 *
	 * USAGE
	 * How is a payment method's getFormBlockType() used? https://mage2.pro/t/691
	 * @used-by \Magento\Payment\Helper\Data::getMethodFormBlock()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Helper/Data.php#L174
	 *
	 * @return string
	 */
	public function getFormBlockType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_convention($this, 'Block_Form', \Df\Payment\Block\Form::class);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's getInfoBlockType() used? https://mage2.pro/t/687
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getInfoBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L25-L32
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getInfoBlockType()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L510-L518
	 *
	 * @return string
	 */
	public function getInfoBlockType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_convention($this, 'Block_Info', \Df\Payment\Block\Info::class);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's getInfoInstance() used? https://mage2.pro/t/696
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L210-L218
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L531-L545
	 * @throws LE
	 * @return InfoInterface|Info|OrderPayment|QuotePayment
	 */
	public function getInfoInstance() {
		if (!$this->_infoInstance) {
			throw new LE(__('We cannot retrieve the payment information object instance.'));
		}
		return $this->_infoInstance;
	}

	/**
	 * 2016-02-09
	 * @override
	 * How is a payment method's getStore() used? https://mage2.pro/t/695
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L49-L53
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L278-L284
	 * @return int
	 */
	public function getStore() {return $this->_storeId;}

	/**
	 * 2016-02-08
	 * @override
	 * How is a payment method's getTitle() used? https://mage2.pro/t/692
	 *
	 * @see \Magento\Payment\Model\MethodInterface::getTitle()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L34-L40
	 * @see \Magento\Payment\Model\Method\AbstractMethod::getTitle()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L732-L740
	 * @return string
	 */
	public function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->settings('title', null, function() {
				return df_class_second($this);
			});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's initialize() used? https://mage2.pro/t/722
	 *
	 * @see \Magento\Payment\Model\MethodInterface::initialize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L361-L372
	 * @see \Magento\Payment\Model\Method\AbstractMethod::initialize()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L838-L852
	 *
	 * @param string $paymentAction
	 * @param object $stateObject
	 * @return $this
	 */
	public function initialize($paymentAction, $stateObject) {return $this;}

	/**
	 * 2016-02-09
	 * @override
	 * https://mage2.pro/t/641
	 * The method isActive() should be removed from the interface
	 * @see \Magento\Payment\Model\MethodInterface,
	 * because it is not used outside of a particular interface's implementation
	 * @see \Magento\Payment\Model\Method\AbstractMethod
	 * and by vault payment methods.
	 *
	 * Но раз уж этот метод присутствует в интерфейсе,
	 * то я его использую в методе @used-by \Df\Payment\Method::settings()
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isActive()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L352-L359
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isActive()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L827-L836
	 *
	 * https://mage2.pro/t/634
	 * https://mage2.pro/t/635
	 * «The @see \Magento\Payment\Model\Method\AbstractMethod::isActive() method
	 * has a wrong PHPDoc type for the $storeId parameter»
	 * «The @see  \Magento\Payment\Model\MethodInterface::isActive() method
	 * has a wrong PHPDoc type for the $storeId parameter»
	 *
	 * @param null|string|int|ScopeInterface $storeId [optional]
	 * @return bool
	 */
	public function isActive($storeId = null) {return $this->settings()->b('enable', $storeId);}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's isAvailable() used? https://mage2.pro/t/721
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L343-L350
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L805-L825
	 *
	 * @param CartInterface $quote [optional]
	 * @return bool
	 */
	public function isAvailable(CartInterface $quote = null) {
		/** @var bool $result */
		$result = $this->isActive($quote ? $quote->getStoreId() : null);
		if ($result) {
			/** @var DataObject $checkResult */
			$checkResult = new DataObject(['is_available' => true]);
			df_dispatch('payment_method_is_active', [
				'result' => $checkResult, 'method_instance' => $this, 'quote' => $quote
			]);
			$result = $checkResult['is_available'];
		}
		return $result;
	}

	/**
	 * 2016-02-11
	 * @override
	 * Насколько я понял, isGateway должно возвращать true,
	 * если процесс оплаты должен происходить непосредственно на странице оформления заказа,
	 * без перенаправления на страницу платёжной системы.
	 * В Российской сборке Magento так пока работает только метод @see Df_Chronopay_Model_Gate,
	 * однако он изготовлен давно и по устаревшей технологии,
	 * и поэтому не является наследником класса @see Df_Payment_Model_Method
	 *
	 * How is a payment method's isGateway() used? https://mage2.pro/t/679
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isGateway()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L160-L166
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isGateway()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L431-L440
	 * @return bool
	 */
	public function isGateway() {return false;}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's isInitializeNeeded() used? https://mage2.pro/t/681
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isInitializeNeeded()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L176-L182
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isInitializeNeeded()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L454-L462
	 * @return bool
	 */
	public function isInitializeNeeded() {return false;}

	/**
	 * 2016-02-11
	 * @override
	 * How is a payment method's isOffline() used? https://mage2.pro/t/680
	 *
	 * @see \Magento\Payment\Model\MethodInterface::isOffline()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L168-L174
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isOffline()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L442-L451
	 * @return bool
	 */
	public function isOffline() {return false;}

	/**
	 * 2016-02-14
	 * @override
	 * How is a payment method's order() used? https://mage2.pro/t/701
	 *
	 * @see \Magento\Payment\Model\MethodInterface::order()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L239-L247
	 * @see \Magento\Payment\Model\Method\AbstractMethod::order()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L585-L601
	 * @param InfoInterface $payment
	 * @param float $amount
	 * @return $this
	 */
	public function order(InfoInterface $payment, $amount) {
		df_should_not_be_here(__METHOD__);
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's refund() used? https://mage2.pro/t/709
	 *
	 * @see \Magento\Payment\Model\MethodInterface::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L269-L277
	 * @see \Magento\Payment\Model\Method\AbstractMethod::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L640-L656
	 * @param InfoInterface $payment
	 * @param float $amount
	 * @return $this
	 */
	public function refund(InfoInterface $payment, $amount) {return $this;}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's setInfoInstance() used? https://mage2.pro/t/697
	 *
	 * @see \Magento\Payment\Model\MethodInterface::setInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L220-L228
	 * @see \Magento\Payment\Model\Method\AbstractMethod::setInfoInstance()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L547-L557
	 * @param InfoInterface|Info|OrderPayment|QuotePayment $info
	 * @return void
	 */
	public function setInfoInstance(InfoInterface $info) {$this->_infoInstance = $info;}

	/**
	 * 2016-02-09
	 * @override
	 * How is a payment method's setStore() used? https://mage2.pro/t/693
	 *
	 * @see \Magento\Payment\Model\MethodInterface::setStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L42-L47
	 * @see \Magento\Payment\Model\Method\AbstractMethod::setStore()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L270-L276
	 * @param int $storeId
	 * @return void
	 */
	public function setStore($storeId) {$this->_storeId = (int)$storeId;}

	/**
	 * 2016-02-12
	 * @override
	 * How is a payment method's validate() used? https://mage2.pro/t/698
	 *
	 * @see \Magento\Payment\Model\MethodInterface::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L230-L237
	 * @see \Magento\Payment\Model\Method\AbstractMethod::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L566-L583
	 * @throws LE
	 * @return $this
	 */
	public function validate() {
		if (!$this->canUseForCountry($this->infoOrderOrQuote()->getBillingAddress()->getCountryId())) {
			throw new LE(__(
				'You can\'t use the payment type you selected to make payments to the billing country.'
			));
		}
		return $this;
	}

	/**
	 * 2016-02-15
	 * @override
	 * How is a payment method's void() used? https://mage2.pro/t/712
	 *
	 * @see \Magento\Payment\Model\MethodInterface::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L288-L295
	 * @see \Magento\Payment\Model\Method\AbstractMethod::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L671-L686
	 * @param InfoInterface $payment
	 * @return $this
	 */
	public function void(InfoInterface $payment) {return $this;}

	/**
	 * 2016-02-12
	 * @return Order|Quote
	 */
	private function infoOrderOrQuote() {
		/** @var InfoInterface|Info|OrderPayment|QuotePayment $info */
		$info = $this->getInfoInstance();
		return $info instanceof OrderPayment ? $info->getOrder() : $info->getQuote();
	}

	/**
	 * 2016-02-09
	 * @param string $key [optional]
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return string|null|\Df\Core\Settings
	 */
	private function settings($key = '', $scope = null) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Core\Settings::sp(df_cc_xpath(
				'df_payment', df_class_second_lc($this), ''
			));
		}
		return df_empty_string($key) ? $this->{__METHOD__} : $this->{__METHOD__}->v($key, $scope);
	}

	/**
	 * 2016-02-12
	 * @used-by \Df\Payment\Method::getInfoInstance()
	 * @used-by \Df\Payment\Method::setInfoInstance()
	 * @var InfoInterface|Info|OrderPayment|QuotePayment
	 */
	private $_infoInstance;

	/**
	 * 2016-02-09
	 * @used-by \Df\Payment\Method::getStore()
	 * @used-by \Df\Payment\Method::setStore()
	 * @var int
	 */
	private $_storeId;
}