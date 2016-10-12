<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\VariationNumber;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class VariationNumberTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class VariationNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests isValid returns true for the correct data
     *
     * @dataProvider isValidWhenValidProvider
     *
     * @param string $txcAppType
     * @param int $variationNoValue
     * @param int $variationNoBusReg
     */
    public function testIsValidWhenValid($txcAppType, $variationNoValue, $variationNoBusReg)
    {
        $sut = new VariationNumber();
        $busReg = new BusRegEntity();
        $busReg->setVariationNo($variationNoBusReg);

        $value = [
            'txcAppType' => $txcAppType,
            'variationNo' => $variationNoValue
        ];

        $context = ['busReg' => $busReg];

        $this->assertEquals(true, $sut->isValid($value, $context));
        $this->assertEmpty($sut->getMessages());
    }

    /**
     * tests isValid returns false for the correct data, as well as the correct error message
     *
     * @dataProvider isValidWhenNotValidProvider
     *
     * @param string $txcAppType
     * @param int $variationNoValue
     * @param int $variationNoBusReg
     * @param string $errorKey
     */
    public function testIsValidWhenNotValid($txcAppType, $variationNoValue, $variationNoBusReg, $errorKey)
    {
        $sut = new VariationNumber();
        $busReg = new BusRegEntity();
        $busReg->setVariationNo($variationNoBusReg);

        $value = [
            'txcAppType' => $txcAppType,
            'variationNo' => $variationNoValue
        ];

        $context = ['busReg' => $busReg];

        $this->assertEquals(false, $sut->isValid($value, $context));
        $messages = $sut->getMessages();
        $this->assertArrayHasKey($errorKey, $messages);
        $this->assertCount(1, $messages);
    }

    /**
     * Provider for testIsValidWhenValid
     *
     * @return array
     */
    public function isValidWhenValidProvider()
    {
        return [
            [BusRegEntity::TXC_APP_NEW, 0, null],
            [BusRegEntity::TXC_APP_CANCEL, 0, 0],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, 1, 0],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, 2, 1],
            [BusRegEntity::TXC_APP_CHARGEABLE, 1, 0],
            [BusRegEntity::TXC_APP_CHARGEABLE, 2, 1],
        ];
    }

    /**
     * Provider for testIsValidWhenNotValid
     *
     * @return array
     */
    public function isValidWhenNotValidProvider()
    {
        return [
            [BusRegEntity::TXC_APP_NEW, 1, null, VariationNumber::NEW_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_CANCEL, 0, 1, VariationNumber::CANCELLATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_CANCEL, 1, 0, VariationNumber::CANCELLATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, 2, 0, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, 0, 0, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, 0, 1, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, 0, 2, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_CHARGEABLE, 2, 0, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_CHARGEABLE, 0, 0, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_CHARGEABLE, 0, 1, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR],
            [BusRegEntity::TXC_APP_CHARGEABLE, 0, 2, VariationNumber::VARIATION_VARIATION_NUMBER_ERROR]
        ];
    }
}
