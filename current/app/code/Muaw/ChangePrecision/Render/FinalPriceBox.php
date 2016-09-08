<?php

/**
 * Created by PhpStorm.
 * User: Chamal
 * Date: 1/25/16
 * Time: 6:14 PM
 */
namespace Muaw\ChangePrecision\Render;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     * Wrap with standard required container
     *
     * @param string $html
     * @return string
     */
    protected function wrapResult($html)
    {
        return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
         'data-role="priceBox" ' .
        'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
        '>' . $html . '</div>';
    }
}