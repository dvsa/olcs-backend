<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Validator;

use Dvsa\Olcs\Transfer\Validators\Vrm as TransferVrmValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Dvsa\Olcs\Api\Service\Nr\Validator\VrmFactory;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Nr\Validator\Vrm;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class VrmFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Validator
 */
class VrmFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockTransferVrm = m::mock(TransferVrmValidator::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with(TransferVrmValidator::class)->andReturn($mockTransferVrm);

        $sut = new VrmFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Vrm::class, $service);
        $this->assertSame($mockTransferVrm, $service->getVrmValidator());
    }
}
