<?php
namespace Muaw\Subscription\Plugin\Checkout\Model\Checkout;


class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['subscribe'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox',
                'options' => [],
                'id' => 'subscribe'
            ],
            'dataScope' => 'shippingAddress.subscribe',
            'label' => 'Opt-In to our news letter',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 250,
            'id' => 'subscribe',
            'value'=>'subscription'
        ];

//        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//        ['shippingAddress']['children']['shipping-address-fieldset']['children']['drop_down'] = [
//            'component' => 'Magento_Ui/js/form/element/select',
//            'config' => [
//                'customScope' => 'shippingAddress',
//                'template' => 'ui/form/field',
//                'elementTmpl' => 'ui/form/element/select',
//                'id' => 'drop-down',
//            ],
//            'dataScope' => 'shippingAddress.drop_down',
//            'label' => 'Drop Down',
//            'provider' => 'checkoutProvider',
//            'visible' => true,
//            'validation' => [],
//            'sortOrder' => 251,
//            'id' => 'drop-down',
//            'options' => [
//                [
//                    'value' => '',
//                    'label' => 'Please Select',
//                ],
//                [
//                    'value' => '1',
//                    'label' => 'First Option',
//                ]
//            ]
//        ];

        return $jsLayout;
    }
}