<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Mapping;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Nr\Mapping\ComplianceEpisodeXmlFactory;
use Dvsa\Olcs\Api\Service\Nr\Mapping\ComplianceEpisodeXml;
use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ComplianceEpisodeXmlFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Mapping
 */
class ComplianceEpisodeXmlFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $config = [
            'nr' => [
                'compliance_episode' => [
                    'xmlNs' => 'xml ns info'
                ]
            ]
        ];

        $mockMapXmlFile = m::mock(MapXmlFile::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->once()->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->once()->with(MapXmlFile::class)->andReturn($mockMapXmlFile);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new ComplianceEpisodeXmlFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(ComplianceEpisodeXml::class, $service);
    }

    public function testCreateServiceMissingConfig()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing INR service config');

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new ComplianceEpisodeXmlFactory();
        $sut->createService($mockSl);
    }
}
