<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\NewAppAlreadyExists;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class NewAppAlreadyExistsTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class NewAppAlreadyExistsTest extends MockeryTestCase
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
        $sut = new NewAppAlreadyExists();

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
            [BusRegEntity::TXC_APP_NEW, $busMock, false],
            [BusRegEntity::TXC_APP_NEW, null, true],
            [BusRegEntity::TXC_APP_CANCEL, $busMock, true],
            [BusRegEntity::TXC_APP_CANCEL, null, true],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, $busMock, true],
            [BusRegEntity::TXC_APP_NON_CHARGEABLE, null, true],
            [BusRegEntity::TXC_APP_CHARGEABLE, $busMock, true],
            [BusRegEntity::TXC_APP_CHARGEABLE, null, true],
        ];
    }
}
