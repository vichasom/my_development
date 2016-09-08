<?php
namespace Df\Config\Plugin\Model\Config\Structure\Element\Iterator;
use Magento\Config\Model\Config\Structure\Element\Iterator\Tab as Sb;
class Tab {
	/**
	 * 2015-11-14
	 * Цель плагина — алфавитное упорядочивание моих модулей
	 * в разделе административных настроек модулей.
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator\Tab::setElements()
	 * @param Sb $sb
	 * @param array(string => array(string => string)) $elements
	 * @param string $scope
	 * @return array()
	 */
	public function beforeSetElements(Sb $sb, array $elements, $scope) {
		/** @var array(string => string)|null $sections */
		$sections = df_a_deep($elements, '_df/children');
		if ($sections) {
			uasort($sections,
				/**
				 * @param array(string => string) $a
				 * @param array(string => string) $b
				 * @return int
				 */
				function($a, $b) {return strcasecmp(df_a($a, 'label'), df_a($b, 'label'));}
			);
			$elements['_df']['children'] = $sections;
		}
		return [$elements, $scope];
	}
}