<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\Nr\MsiResponseFactory;
use Dvsa\Olcs\Api\Service\Nr\MsiResponse;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Http\Client as RestClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Xml\XmlNodeBuilder;

/**
 * Class MsiResponseFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class MsiResponseFactoryTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testCreateServiceNoConfig()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new MsiResponseFactory();
        $sut->createService($mockSl);
    }

    public function testCreateService()
    {
        $config = [
            'nr' => [
                'msi_response' => [
                    'parent_node' => 'node',
                    'ns' => 'ns'
                ]
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new MsiResponseFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(MsiResponse::class, $service);
        $this->assertInstanceOf(XmlNodeBuilder::class, $service->getXmlBuilder());
    }
}
