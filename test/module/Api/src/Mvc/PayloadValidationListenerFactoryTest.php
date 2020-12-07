<?php

namespace Dvsa\OlcsTest\Api\Mvc;

use Dvsa\Olcs\Api\Mvc\PayloadValidationListener;
use Dvsa\Olcs\Api\Mvc\PayloadValidationListenerFactory;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * PayloadValidationListenerFactory Test
 */
class PayloadValidationListenerFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockAb = m::mock(AnnotationBuilder::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('TransferAnnotationBuilder')->andReturn($mockAb);

        $sut = new PayloadValidationListenerFactory();

        $listener = $sut->createService($mockSl);

        $this->assertInstanceOf(PayloadValidationListener::class, $listener);
    }
}
