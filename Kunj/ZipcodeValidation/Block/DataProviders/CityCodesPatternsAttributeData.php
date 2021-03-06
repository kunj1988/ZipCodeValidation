<?php

declare(strict_types=1);

namespace Kunj\ZipcodeValidation\Block\DataProviders;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CityCodesPatternsAttributeData implements ArgumentInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get serialized post codes
     *
     * @return string
     */
    public function getSerializedCities(): string
    {
        return $this->serializer->serialize($this->getCityOptions());
    }

    /**
     * @return array
     */
    public function getCityOptions() {
        return [
            '544'=>[
                'Ahmedabad' => [
                    'code' => 'Ahmedabad',
                    'name' => 'Ahmedabad',
                ],
                'Surat' => [
                    'code' => 'Surat',
                    'name' => 'Surat',
                ],
                'Rajkot' => [
                    'code' => 'Rajkot',
                    'name' => 'Rajkot',
                ]
            ],
            '534'=>[
                'Vijayawada' => [
                    'code' => 'Vijayawada',
                    'name' => 'Vijayawada',
                ],
                'Kurnool' => [
                    'code' => 'Kurnool',
                    'name' => 'Kurnool',
                ],
                'Ongole' => [
                    'code' => 'Ongole',
                    'name' => 'Ongole',
                ]
            ]
        ];
    }

}