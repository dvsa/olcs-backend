<?php

namespace Dvsa\OlcsTest\Api\Mvc;

use Dvsa\Olcs\Api\Mvc\PayloadValidationListener;
use Dvsa\Olcs\Api\Mvc\PayloadValidationListenerFactory;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PayloadValidationListenerFactory Test
 */
class PayloadValidationListenerFactoryTest extends MockeryTestCase
{
    public function testInvoke()
    {
        $mockAb = m::mock(AnnotationBuilder::class);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('TransferAnnotationBuilder')->andReturn($mockAb);

        $sut = new PayloadValidationListenerFactory();

        $listener = $sut->__invoke($mockSl, PayloadValidationListenerFactory::class);

        $this->assertInstanceOf(PayloadValidationListener::class, $listener);
    }
}
