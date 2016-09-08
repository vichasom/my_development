<?php

namespace Muaw\ProductCategory\Block;

class ProductCategory extends \Magento\Framework\View\Element\Template
{

    protected $_categoryHelper;
    protected $_registry;
    protected $_categoryCollection;
    protected $_urlinterface;
    protected $_currency;
    protected $_priceHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     * @internal param \Magento\Catalog\Helper\Category $categoryHelper
     * @internal param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Pricing\Helper\Data $priceHelper

    )
    {

        $this->_registry = $registry;
        $this->_categoryCollection = $categoryCollection;
        $this->_currency = $currency;
        $this->_priceHelper = $priceHelper;


        parent::__construct($context);

        $this->_urlinterface = $this->_urlBuilder;
    }

    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * @param $_lastCategory
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryById($_lastCategory)
    {
        $category = $this->_categoryCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', $_lastCategory)
            ->getFirstItem();
        return $category;
    }

    /**
     * @return mixed
     */
    public function getCurrentProductCatDescription()
    {
        $_product = $this->_registry->registry('current_product');
        $_catIds = $_product->getCategoryIds();
        $_lastCategory = end($_catIds);
        $category = $this->getCategoryById($_lastCategory);
        $categoryDescription = $category->getDescription();

        return $categoryDescription;
    }
	
	
	/** Product ID **/
	
	public function getCurrentProductId()
    {
        $_product = $this->_registry->registry('current_product');
        $_id = $_product->getId();
        return $_id;
    }



    /**
     * @return mixed
     */
	 
	 
	 
    public function getCurrentProductName()
    {
        $_product = $this->_registry->registry('current_product');
        $_name = $_product->getName();
        return $_name;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlinterface->getCurrentUrl();
    }

    /**
     * @return mixed
     */
    public function getCurrentProductDescription()
    {
        $_product = $this->_registry->registry('current_product');
        return $_product->getDescription();
    }

    /**
     * @return mixed
     */
    public function getProductAmount(){
        $_product = $this->_registry->registry('current_product');
        $roundedPrice = ceil($_product->getFinalPrice());
        return $roundedPrice;

    }

    public function getCurrentProductPrice(){
        $_product = $this->_registry->registry('current_product');

        // TODO: find a better way to remove decimal points from the price
        $roundedPrice = ceil($_product->getFinalPrice());
        $priceWithCurrency = $this->_priceHelper->currency($roundedPrice,true,true);
        $price = explode(".", $priceWithCurrency);
        return $price[0];
    }

}
