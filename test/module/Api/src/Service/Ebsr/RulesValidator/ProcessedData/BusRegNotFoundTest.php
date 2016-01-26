<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\BusRegNotFound;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class BusRegNotFoundTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class BusRegNotFoundTest extends MockeryTestCase
{
    /**
     * tests whether a new application is prevented from reusing an existing number
     *
     * @dataProvider isValidProvider
     *
     * @param string $txcAppType
     * @param BusRegEntity|null $busReg
     * @param bool $valid
     */
    public function testIsValid($txcAppType, $busReg, $valid)
    {
        $sut = new BusRegNotFound();

        $value = [
            'txcAppType' => $txcAppType,
            'existingRegNo' => '1234/567'
        ];

        $context = ['busReg' => $busReg];

        $this->assertEquals($valid, $sut->isValid($value, $context));
    }

    /**
     * Provider for testIsValid
     *
     * @return array
     */
    public function isValidProvider()
    {
        $busMock = m::mock(BusRegEntity::class);

        return [
            ['new', $busMock, true],
            ['new', null, true],
            ['cancel', $busMock, true],
            ['cancel', null, false],
            ['nonChargeableChange', $busMock, true],
            ['nonChargeableChange', null, false],
            ['chargeableChange', $busMock, true],
            ['chargeableChange', null, false],
        ];
    }
}
