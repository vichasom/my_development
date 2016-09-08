<?php
/**
 * Created by PhpStorm.
 * User: Janaka Dombawela
 * Date: 4/29/2016
 * Time: 11:56 AM
 */

namespace Muaw\Subscription\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject as Object;
use Magento\FrameWork\App\Action\Action as Action;

class SaveSubscription implements ObserverInterface
{
//    const AJAX_PARAM_NAME = 'infscroll';
//    const AJAX_HANDLE_NAME = 'infscroll_ajax_request';

    /**
     * Https request
     *
     * @var \Zend\Http\Request
     */
//    protected $_request;
//    protected $_layout;
//    protected $_cache;

    /**
     * @param Item $item
     */
//    public function __construct(
//        \Magento\Framework\View\Element\Context $context,
//        \BelVG\Infscroll\Helper\Cache $cache
//    )
//    {
//        $this->_layout = $context->getLayout();
//        $this->_request = $context->getRequest();
//        $this->_cache = $cache;
//    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        print_r($this->getRequest()->getParams());
//        $data = $observer->getEvent();
    }

}