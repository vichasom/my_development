<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/20/2016
 * Time: 11:52 AM
 */


namespace Muaw\Newsletter\Block;

use Magento\Framework\View\Element\Template;

class Subscribe extends Template
{
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function beforeToHtml(\Muaw\Newsletter\Block\Subscribe $originalBlock)
    {
        $originalBlock->setTemplate('Muaw_Newsletter::subscribe.phtml');
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', ['_secure' => true]);
    }
}