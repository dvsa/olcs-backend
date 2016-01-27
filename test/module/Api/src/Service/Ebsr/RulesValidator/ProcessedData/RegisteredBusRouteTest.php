<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\RegisteredBusRoute;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class RegisteredBusRouteTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class RegisteredBusRouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests whether a new application is prevented from reusing an existing number
     *
     * @dataProvider isValidProvider
     *
     * @param string $txcAppType
     * @param string $status
     * @param bool $valid
     */
    public function testIsValid($txcAppType, $status, $valid)
    {
        $sut = new RegisteredBusRoute();
        $busReg = new BusRegEntity();
        $busReg->setStatus(new RefData($status));

        $value = [
            'txcAppType' => $txcAppType
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
        return [
            ['new', BusRegEntity::STATUS_NEW, true],
            ['new', BusRegEntity::STATUS_VAR, true],
            ['new', BusRegEntity::STATUS_CANCEL, true],
            ['new', BusRegEntity::STATUS_ADMIN, true],
            ['new', BusRegEntity::STATUS_REGISTERED, true],
            ['new', BusRegEntity::STATUS_REFUSED, true],
            ['new', BusRegEntity::STATUS_WITHDRAWN, true],
            ['new', BusRegEntity::STATUS_CNS, true],
            ['new', BusRegEntity::STATUS_CANCELLED, true],
            ['cancel', BusRegEntity::STATUS_NEW, false],
            ['cancel', BusRegEntity::STATUS_VAR, false],
            ['cancel', BusRegEntity::STATUS_CANCEL, false],
            ['cancel', BusRegEntity::STATUS_ADMIN, false],
            ['cancel', BusRegEntity::STATUS_REGISTERED, true],
            ['cancel', BusRegEntity::STATUS_REFUSED, false],
            ['cancel', BusRegEntity::STATUS_WITHDRAWN, false],
            ['cancel', BusRegEntity::STATUS_CNS, false],
            ['cancel', BusRegEntity::STATUS_CANCELLED, false],
            ['nonChargeableChange', BusRegEntity::STATUS_NEW, false],
            ['nonChargeableChange', BusRegEntity::STATUS_VAR, false],
            ['nonChargeableChange', BusRegEntity::STATUS_CANCEL, false],
            ['nonChargeableChange', BusRegEntity::STATUS_ADMIN, false],
            ['nonChargeableChange', BusRegEntity::STATUS_REGISTERED, true],
            ['nonChargeableChange', BusRegEntity::STATUS_REFUSED, false],
            ['nonChargeableChange', BusRegEntity::STATUS_WITHDRAWN, false],
            ['nonChargeableChange', BusRegEntity::STATUS_CNS, false],
            ['nonChargeableChange', BusRegEntity::STATUS_CANCELLED, false],
            ['chargeableChange', BusRegEntity::STATUS_NEW, false],
            ['chargeableChange', BusRegEntity::STATUS_VAR, false],
            ['chargeableChange', BusRegEntity::STATUS_CANCEL, false],
            ['chargeableChange', BusRegEntity::STATUS_ADMIN, false],
            ['chargeableChange', BusRegEntity::STATUS_REGISTERED, true],
            ['chargeableChange', BusRegEntity::STATUS_REFUSED, false],
            ['chargeableChange', BusRegEntity::STATUS_WITHDRAWN, false],
            ['chargeableChange', BusRegEntity::STATUS_CNS, false],
            ['chargeableChange', BusRegEntity::STATUS_CANCELLED, false]
        ];
    }
}
