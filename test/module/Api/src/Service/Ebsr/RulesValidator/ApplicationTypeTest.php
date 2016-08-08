<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ApplicationType;
use PHPUnit_Framework_TestCase as TestCase;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission;

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
            $this->assertEquals($error, key($sut->getMessages()));
        }
    }

    /**
     * Data provider for testIsValid
     *
     * @return array
     */
    public function provideIsValid()
    {
        return [
            [
                ['txcAppType' => 'new'],
                ['submissionType' => EbsrSubmission::DATA_REFRESH_SUBMISSION_TYPE],
                false,
                ApplicationType::REFRESH_SUBMISSION_ERROR
            ],
            [
                ['txcAppType' => 'new'],
                ['submissionType' => EbsrSubmission::NEW_SUBMISSION_TYPE],
                true
            ],
            [
                ['txcAppType' => 'nonChargeableChange'],
                ['submissionType' => EbsrSubmission::DATA_REFRESH_SUBMISSION_TYPE],
                true
            ],
            [
                ['txcAppType' => 'nonChargeableChange'],
                ['submissionType' => EbsrSubmission::NEW_SUBMISSION_TYPE],
                false,
                ApplicationType::NEW_SUBMISSION_ERROR
            ],
            [
                ['txcAppType' => 'chargeableChange'],
                ['submissionType' => EbsrSubmission::DATA_REFRESH_SUBMISSION_TYPE],
                false,
                ApplicationType::REFRESH_SUBMISSION_ERROR
            ],
            [
                ['txcAppType' => 'chargeableChange'],
                ['submissionType' => EbsrSubmission::NEW_SUBMISSION_TYPE],
                true
            ],
            [
                ['txcAppType' => 'cancel'],
                ['submissionType' => EbsrSubmission::DATA_REFRESH_SUBMISSION_TYPE],
                false,
                ApplicationType::REFRESH_SUBMISSION_ERROR
            ],
            [
                ['txcAppType' => 'cancel'],
                ['submissionType' => EbsrSubmission::NEW_SUBMISSION_TYPE],
                true
            ]
        ];
    }
}
