<?php

namespace Kunj\ZipcodeValidation\Plugin;

use Magento\Store\Model\ScopeInterface;

class CheckoutProcessor
{
    /**
     * Payment method factory
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_paymentMethodFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_paymentMethodFactory = $paymentMethodFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Checkout LayoutProcessor after process plugin.
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $processor, $jsLayout)
    {
        $paymentActiveMethods = $this->getActiveMethods();
        $postCodeComponent = 'Kunj_ZipcodeValidation/js/form/element/post-code';
        $postCodeValidation = [
            'required-entry custom-zip-code-validate' => true,

        ];
        $payment = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment'];
        $paymentsList = &$payment['children']['payments-list']['children'];
        $paymentRenders = &$payment['children']['renders']['children'];
        $cityField = [
                'component' => 'Kunj_ZipcodeValidation/js/form/element/city',
                'config' => [
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select',
                ],
                'validation' => [
                    'required-entry' => true,
                ],
                'filterBy' => [
                    'target' => '${ $.provider }:${ $.parentScope }.region_id',
                    'field' => 'region_id',
                ],
            ];
        $shippingAddressFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
        $paymentAddressFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children'];
        if(is_array($paymentActiveMethods)) {
            foreach ($paymentActiveMethods as $key => $data) {

                $paymentForm = $key.'-form';
                if(isset($paymentsList[$paymentForm])) {
                    $cityProvider = &$paymentsList[$paymentForm]['children']['form-fields']['children']['city']['provider'];

                    $paymentsList[$paymentForm]['children']['form-fields']['children']['postcode']['component'] = &$postCodeComponent;
                    $paymentsList[$paymentForm]['children']['form-fields']['children']['postcode']['validation'] = &$postCodeValidation;
                    $paymentsList[$paymentForm]['children']['form-fields']['children']['city']['component'] = $cityField['component'];
                    $paymentsList[$paymentForm]['children']['form-fields']['children']['city']['config']['elementTmpl'] = $cityField['config']['elementTmpl'];
                    $paymentsList[$paymentForm]['children']['form-fields']['children']['city']['filterBy'] = $cityField['filterBy'];
                    $paymentsList[$paymentForm]['children']['form-fields']['children']['city']['deps'] = [
                        $cityProvider
                    ];
                    $paymentsList[$paymentForm]['children']['form-fields']['children']['city']['imports'] = [
                        'initialOptions' => 'index ='.$cityProvider.':dictionaries.city',
                        'setOptions' => 'index ='.$cityProvider.':dictionaries.city',
                    ];

                }
            }
        }

        $shippingAddressFields['postcode']['validation'] = $postCodeValidation;
        $shippingAddressFields['postcode']['component'] = $postCodeComponent;
        $paymentAddressFields['postcode']['validation'] = $postCodeValidation;
        $paymentAddressFields['postcode']['component'] = $postCodeComponent;
        $cityProvider = $shippingAddressFields['city']['provider'];
        $shippingAddressFields['city']['component'] = $cityField['component'];
        $shippingAddressFields['city']['config']['elementTmpl'] = $cityField['config']['elementTmpl'];
        $shippingAddressFields['city']['filterBy'] = $cityField['filterBy'];
        $shippingAddressFields['city']['deps'] = [
            $cityProvider
        ];
        $shippingAddressFields['city']['imports'] = [
            'initialOptions' => 'index ='.$cityProvider.':dictionaries.city',
            'setOptions' => 'index ='.$cityProvider.':dictionaries.city',
        ];

        return $jsLayout;
    }

    /**
     * Retrieve active system payments
     *
     * @return array
     * @api
     */
    public function getActiveMethods()
    {
        $methods = [];
        foreach ($this->scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null) as $code => $data) {
            if (isset($data['active'], $data['title']) && (bool)$data['active']) {
                /** @var MethodInterface $methodModel Actually it's wrong interface */
                $methodModel = $this->_paymentMethodFactory->create($data['model']);
                $methodModel->setStore(null);
                if ($methodModel->getConfigData('active', null)) {
                    $methods[$code] = $data['title'];
                }
            }
        }
        return $methods;
    }

}