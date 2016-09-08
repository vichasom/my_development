<?php
/**
 * Created by PhpStorm.
 * User: Janaka Dombawela
 * Date: 4/29/2016
 * Time: 11:51 AM
 */


namespace Muaw\Subscription\Model;
class Observer
{
//    protected $_verpageData;
    protected $_registry = null;

    public function __construct(
//        \Muaw\Subscribe\Helper\Data $verpageData,
        \Magento\Framework\Registry $registry
    )
    {
//        $this->_verpageData = $verpageData;
        $this->_registry = $registry;
    }

    public function verificate(\Magento\Framework\Event\Observer $observer)
    {

    }
}