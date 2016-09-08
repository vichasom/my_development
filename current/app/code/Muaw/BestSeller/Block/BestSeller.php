<?php
/**
 * Created by PhpStorm.
 * User: Chamal
 * Date: 2/16/16
 * Time: 2:12 PM
 */

namespace Muaw\BestSeller\Block;

class BestSeller extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;
    protected $_collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }


    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getBestSellerData()
    {
        $collection = $this->_collectionFactory->create()->setModel(
            'Magento\Catalog\Model\Product'
        );

        return $collection;
    }
}