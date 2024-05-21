<?php

namespace Dvsa\OlcsTest\Api\Service\DvlaSearch;

use Dvsa\Olcs\Api\Service\DvlaSearch\DvlaSearchServiceFactory;
use Dvsa\Olcs\Api\Service\DvlaSearch\DvlaSearchService as DvlaSearchServiceClient;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

class DvlaSearchServiceFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $config = [
            'dvla_search' => [
                'base_uri' => 'http://localhost',
                'proxy' => 'http://localhost',
                'api_key' => 'abc123'
            ]
        ];

        $logger = new \Laminas\Log\Logger();

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn($config);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($logger);

        $sut = new DvlaSearchServiceFactory();
        $service = $sut->__invoke($mockSl, DvlaSearchServiceClient::class);

        $this->assertInstanceOf(DvlaSearchServiceClient::class, $service);
    }
}
