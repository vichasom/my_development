<?php
/**
 * Copyright © 2015 Muaw . All rights reserved.
 */
namespace Muaw\Related\Block;
class Related extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Quote|null
     */
    protected $_quote = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_checkoutSession;

    protected $_product;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product $product,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_product = $product;
        parent::__construct($context, $data);
    }


    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }

    public function getRelatedProducts() {
        $items = $this->getQuote()->getAllVisibleItems();
        $relatedIds = array();
        foreach ($items as $item) {
            $pr = $this->_product->load($item->getProductId());
            $ids = $pr->getRelatedProductIds();
            $relatedIds = array_unique(array_merge($relatedIds,$ids));
        }
        return $relatedIds;
    }

    public function getCartProductIds() {
        $items = $this->getQuote()->getAllVisibleItems();
        $productIds = array();
        foreach ($items as $item) {
            $productIds = $item->getProductId();
        }
        return $productIds;
    }

    /**
     * Return HTML block with tier price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }

}
