<?php

namespace Dvsa\OlcsTest\Api\Service\Toggle;

use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Toggle\ToggleServiceFactory;

/**
 * Class ToggleServiceFactoryTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ToggleServiceFactoryTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testCreateService()
    {
        $config = [
            'feature_toggle' => [
                'Test Config' => [
                    'name' => 'test name',
                    'conditions' => [],
                    'status' => 'inactive'
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->once()->with('Config')->andReturn($config);

        $sut = new ToggleServiceFactory();
        $this->assertInstanceOf(ToggleService::class, $sut->createService($mockSl));
    }
}
