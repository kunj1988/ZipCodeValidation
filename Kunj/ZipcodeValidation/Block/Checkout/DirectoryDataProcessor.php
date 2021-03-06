<?php

namespace Kunj\ZipcodeValidation\Block\Checkout;

use Kunj\ZipcodeValidation\Block\DataProviders\CityCodesPatternsAttributeData;
use Kunj\ZipcodeValidation\Block\DataProviders\PostCodesPatternsAttributeData;

class DirectoryDataProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var CityCodesPatternsAttributeData
     */
    private $cityCodesPatternsAttributeData;

    /**
     * @var PostCodesPatternsAttributeData
     */
    private $codesPatternsAttributeData;

    /**
     * DirectoryDataProcessor constructor.
     * @param CityCodesPatternsAttributeData $cityCodesPatternsAttributeData
     */
    public function __construct(
        CityCodesPatternsAttributeData $cityCodesPatternsAttributeData,
        PostCodesPatternsAttributeData $codesPatternsAttributeData
    ) {
        $this->codesPatternsAttributeData = $codesPatternsAttributeData;
        $this->cityCodesPatternsAttributeData = $cityCodesPatternsAttributeData;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if (isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
            $cityOptions = $this->cityCodesPatternsAttributeData->getCityOptions();
            $postCodeData = $this->codesPatternsAttributeData->getPostCodeData();
            $options = [];
            foreach ($cityOptions as $key => $cities) {

                foreach ($cities as $cityKey => $city) {
                    $zipCode = (isset($postCodeData[$cityKey])) ? $postCodeData[$cityKey]:[];
                    $options[$key][] = [
                        'value' => $city['code'],
                        'label' => $city['name'],
                        'zip_code' => $zipCode
                    ];
                }
            }
            $jsLayout['components']['checkoutProvider']['dictionaries']['city'] = $options;
        }
        return $jsLayout;
    }

}