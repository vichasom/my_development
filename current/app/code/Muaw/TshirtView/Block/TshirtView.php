<?php
/**
 * Created by PhpStorm.
 * User: Chamal
 * Date: 3/1/16
 * Time: 9:51 AM
 */

/**
 * Copyright © 2015 Muaw . All rights reserved.
 */
namespace Muaw\TshirtView\Block;

class TshirtView extends \Magento\Framework\View\Element\Template
{

    protected $_registry;

    protected  $_storeManager;

    /**
     * TshirtView constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore(){
        return $this->_storeManager->getStore();
    }

    /**
     * @return string
     */
    public function getProductArtworkPath()
    {
        $_product = $this->getCurrentProduct();
        $currentStore = $this->getCurrentStore();
        $_sticker = $_product->getResource()->getAttributeRawValue($_product->getId(), 'admire_sticker', $currentStore);
        $_media_path = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $_media_path . 'catalog/product' . $_sticker;
    }

    /**
     * @return mixed
     */
    public function getArtwork()
    {
        $_product = $this->getCurrentProduct();
        $currentStore = $this->getCurrentStore();
        $_sticker = $_product->getResource()->getAttributeRawValue($_product->getId(), 'admire_sticker', $currentStore);
        return $_sticker;
    }

    /**
     * @return mixed
     */
    public function getProductType()
    {
        $_product = $this->getCurrentProduct();
        return $_product->getTypeId();
    }

    
}