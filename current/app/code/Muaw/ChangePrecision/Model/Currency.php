<?php

/**
 * Created by PhpStorm.
 * User: Chamal
 * Date: 1/25/16
 * Time: 4:35 PM
 */
namespace Muaw\ChangePrecision\Model;

class Currency extends \Magento\Directory\Model\Currency
{
    /**
     * Format price to currency format
     *
     * @param float $price
     * @param array $options
     * @param bool $includeContainer
     * @param bool $addBrackets
     * @return string
     */
    public function format($price, $options = [], $includeContainer = true, $addBrackets = false)
    {
        return $this->formatPrecision($price, 0, $options, $includeContainer, $addBrackets);
    }
}