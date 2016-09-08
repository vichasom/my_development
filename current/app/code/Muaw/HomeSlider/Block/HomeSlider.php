<?php
namespace Muaw\HomeSlider\Block;

use Magento\Customer\Model\Context as CustomerContext;

class HomeSlider extends \Magento\Framework\View\Element\Template
{

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
        array $data = []
    )
    {

        parent::__construct($context, $data);
    }



}