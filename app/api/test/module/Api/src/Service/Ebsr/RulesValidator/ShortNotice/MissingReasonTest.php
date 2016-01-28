<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ShortNotice;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class MissingReasonTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator\ShortNotice
 */
class MissingReasonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests whether the short notice section exists correctly
     *
     * @dataProvider isValidProvider
     *
     * @param string $isShortNotice
     * @param array $busShortNotice
     * @param bool $valid
     */
    public function testIsValid($isShortNotice, $busShortNotice, $valid)
    {
        $sut = new MissingReason();
        $busReg = new BusRegEntity();
        $busReg->setIsShortNotice($isShortNotice);

        $context = ['busReg' => $busReg];

        $value = ['busShortNotice' => $busShortNotice];

        $this->assertEquals($valid, $sut->isValid($value, $context));
    }

    /**
     * Provider for testIsValid
     *
     * @return array
     */
    public function isValidProvider()
    {
        //some of these examples are valid only because the fields aren't set
        $validSn1 = ['notAvailableChange' => 'Y', 'timetableChange' => 'Y', 'someElement' => 'someValue'];
        $validSn2 = ['notAvailableChange' => 'N', 'timetableChange' => 'Y', 'someElement' => 'someValue'];
        $validSn3 = ['notAvailableChange' => 'Y', 'timetableChange' => 'N', 'someElement' => 'someValue'];
        $validSn4 = ['notAvailableChange' => 'N', 'timetableChange' => 'N', 'someElement' => 'someValue'];
        $validSn5 = ['timetableChange' => 'Y', 'someElement' => 'someValue'];
        $validSn6 = ['notAvailableChange' => 'Y', 'someElement' => 'someValue'];
        $validSn7 = ['timetableChange' => 'N', 'someElement' => 'someValue'];
        $validSn8 = ['notAvailableChange' => 'N', 'someElement' => 'someValue'];
        $validSn9 = ['notAvailableChange' => 'Y', 'timetableChange' => 'Y'];

        $invalidSn1 = ['notAvailableChange' => 'N', 'timetableChange' => 'N'];

        return [
            ['Y', $validSn1, true],
            ['Y', $validSn2, true],
            ['Y', $validSn3, true],
            ['Y', $validSn4, true],
            ['Y', $validSn5, true],
            ['Y', $validSn6, true],
            ['Y', $validSn7, true],
            ['Y', $validSn8, true],
            ['Y', $validSn9, true],
            ['N', $invalidSn1, true], //record not short notice
            ['Y', $invalidSn1, false]
        ];
    }
}
