<?php
namespace Df\Framework\Data\Form\Element\Select;
use Df\Framework\Data\Form\Element\Select;
class Integer extends Select {
	/**
	 * 2016-01-29
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Select::getValues()
	 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L62
	 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L124
	 * @return array(array(string => string))
	 */
	public function getValues() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a_to_options(range(
				df_fe_fc_i($this, 'dfMin'), df_fe_fc_i($this, 'dfMax')
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-01-29
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Select::onFormInitialized()
	 * @return void
	 */
	public function onFormInitialized() {
		$this->addClass('df-dropdown-number');
		parent::onFormInitialized();
	}
}