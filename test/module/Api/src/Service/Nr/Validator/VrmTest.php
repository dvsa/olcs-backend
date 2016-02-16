<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Validator;

use Dvsa\Olcs\Api\Service\Nr\Validator\Vrm;
use Dvsa\Olcs\Transfer\Validators\Vrm as TransferVrmValidator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class VrmTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Validator
 */
class VrmTest extends TestCase
{
    /**
     * @dataProvider provideIsValid
     * @param $vrm
     * @param $valid
     * @param string $error
     */
    public function testIsValid($vrm, $valid, $error = '')
    {
        $value = [
            'vrm' => $vrm
        ];

        $mockTransferVrm = m::mock(TransferVrmValidator::class);
        $mockTransferVrm->shouldReceive('isValid')->with($vrm)->andReturn($valid);

        $sut = new Vrm();
        $sut->setVrmValidator($mockTransferVrm);

        $this->assertEquals($valid, $sut->isValid($value));

        if ($error != '') {
            $message = current($sut->getMessages());
            $this->assertEquals($error, $message);
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
                'KHW004',
                true
            ],
            [
                'kHw004',
                false,
                'VRM is not in the correct format'
            ],
            [
                'ABCDEFGH',
                false,
                'VRM is not in the correct format'
            ],
        ];
    }
}
