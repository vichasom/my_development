<?php
/**
 * Created by PhpStorm.
 * User: Janaka Dombawela
 * Date: 5/3/2016
 * Time: 4:37 PM
 */

namespace Muaw\Subscription\Model\Checkout;

class ShippingInformationManagementPlugin
{

    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    )
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $subscribe = $extAttributes->getSubscribe();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setSubscribe($subscribe);
    }
}