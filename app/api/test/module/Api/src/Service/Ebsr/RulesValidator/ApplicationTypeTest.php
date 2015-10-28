<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ApplicationType;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ApplicationTypeTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator
 */
class ApplicationTypeTest extends TestCase
{
    /**
     * @dataProvider provideIsValid
     * @param $input
     * @param $context
     * @param $valid
     * @param string $error
     */
    public function testIsValid($input, $context, $valid, $error = '')
    {
        $sut = new ApplicationType();
        $this->assertEquals($valid, $sut->isValid($input, $context));

        if ($error != '') {
            $message = current($sut->getMessages());
            $this->assertEquals($error, $message);
        }
    }

    public function provideIsValid()
    {
        return [
            [
                ['txcAppType' => 'new'],
                ['submissionType' => 'ebsrt_refresh'],
                false,
                'Application type for a data refresh must be Non chargeable change'
            ],
            [
                ['txcAppType' => 'nonChargeableChange'],
                ['submissionType' => 'ebsrt_refresh'],
                true,
            ],
        ];
    }
}
