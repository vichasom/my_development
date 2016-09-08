<?php
namespace Muaw\LatestProducts\Block;

use Magento\Customer\Model\Context as CustomerContext;

class LatestProducts extends \Magento\Catalog\Block\Product\AbstractProduct
{

    protected $_productcollection;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productcollection,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    )
    {
        $this->_productcollection = $productcollection;
        $this->urlHelper = $urlHelper;
        $this->_catalogProductVisibility = $catalogProductVisibility;

        parent::__construct($context, $data);
    }

    public function getLatestProducts()
    {
        $collection = $this->_productcollection->create()
            ->addAttributeToFilter('status', '1')
            ->addAttributeToSort('created_at', 'desc');
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->setPageSize(5)
            ->setCurPage(1);

        return $collection;
    }

    public function getBestSellingProducts()
    {
        $collection = $this->_productcollection->create()
            ->addAttributeToFilter('status', '1')
            ->setOrder('ordered_qty', 'desc');
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->setPageSize(5)
            ->setCurPage(1);

        return $collection;
    }

    /**
     * same as getBestSellingProducts but with page size of 4
     * @return $this
     */
    public function getPopularProducts()
    {
        $collection = $this->_productcollection->create()
            ->addAttributeToFilter('status', '1')
            ->setOrder('ordered_qty', 'desc');
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->setPageSize(4)
            ->setCurPage(1);

        return $collection;
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

}