<?php

namespace Dvsa\OlcsTest\Api\Service\DvlaSearch;

use Dvsa\Olcs\Api\Service\DvlaSearch\DvlaSearchServiceFactory;
use Dvsa\Olcs\DvlaSearch\Service\Client as DvlaSearchServiceClient;
use Zend\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class DvlaSearchServiceFactoryTest extends TestCase
{

    public function testCreateService()
    {
        $config = [
            'dvla_search' => [
                'base_uri' => 'http://localhost',
                'proxy' => 'http://localhost',
                'api_key' => 'abc123'
            ]
        ];

        $logger = new \Zend\Log\Logger();

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $mockSl->shouldReceive('get')->with('logger')->andReturn($logger);

        $sut = new DvlaSearchServiceFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(DvlaSearchServiceClient::class, $service);
    }
}
