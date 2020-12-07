<?php

/**
 * OlcsBlameableListenerFactory Test
 */
namespace Dvsa\OlcsTest\Api\Mvc;

use Dvsa\Olcs\Api\Mvc\OlcsBlameableListener;
use Dvsa\Olcs\Api\Mvc\OlcsBlameableListenerFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * OlcsBlameableListenerFactory Test
 */
class OlcsBlameableListenerFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class);

        $sut = new OlcsBlameableListenerFactory();

        $listener = $sut->createService($mockSl);

        $this->assertInstanceOf(OlcsBlameableListener::class, $listener);
        $this->assertSame($mockSl, $listener->getServiceLocator());
    }
}
