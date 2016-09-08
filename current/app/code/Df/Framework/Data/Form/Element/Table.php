<?php
namespace Df\Framework\Data\Form\Element;
abstract class Table extends Hidden {
	/**
	 * 2015-12-16
	 * @used-by \Df\Framework\Data\Form\Element\Table::onFormInitialized()
	 * @return string[]
	 */
	abstract protected function columns();

	/**
	 * @override
	 * @see \Df\Framework\Data\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		df_fe_init($this, __CLASS__, 'Df_Core::lib/Handsontable/main.css', [
			'columns' => $this->columns()
		]);
	}
}