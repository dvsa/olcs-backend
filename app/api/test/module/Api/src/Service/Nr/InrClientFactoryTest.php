<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\Nr\InrClientFactory;
use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Http\Client as RestClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InrClientFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr
 */
class InrClientFactoryTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testCreateServiceNoConfig()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new InrClientFactory();
        $sut->createService($mockSl);
    }

    public function testCreateService()
    {
        $config = [
            'inr_service' => [
                'uri' => 'http://testServiceAddress',
                'options' => ['timeout' => 30]
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new InrClientFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(InrClientInterface::class, $service);
    }
}
