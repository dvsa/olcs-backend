<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\EffectiveDate;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class EffectiveDateTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator
 */
class EffectiveDateTest extends TestCase
{
    /**
     * @dataProvider isValidProvider
     */
    public function testIsValid($data, $validity)
    {
        $sut = new EffectiveDate();
        $this->assertEquals($validity, $sut->isValid($data));
    }

    public function isValidProvider()
    {
        $today = strtotime(date('Y-m-d'));

        return [
            [['txcAppType' => 'new', 'effectiveDate' => date('Y-m-d', $today - 86400)], false],
            [['txcAppType' => 'new', 'effectiveDate' => date('Y-m-d', $today + 86400)], true],
            [['txcAppType' => 'other', 'effectiveDate' => date('Y-m-d', $today - 86400)], true],
        ];
    }
}
