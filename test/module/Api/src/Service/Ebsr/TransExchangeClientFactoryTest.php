<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClientFactory;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;

class TransExchangeClientFactoryTest extends TestCase
{
    public function testInvokeNoConfig()
    {
        $this->expectException(\RuntimeException::class);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn([]);

        $sut = new TransExchangeClientFactory();
        $sut->__invoke($mockSl, TransExchangeClient::class);
    }

    public function testInvoke()
    {
        $config = [
            'transexchange_publisher' => [
                'uri' => 'http://localhost:8080/txc/',
                'options' => ['argseparator' => '~'],
                'template_file' => 'template.xml'
            ]
        ];

        $mockSpec = m::mock(SpecificationInterface::class);
        $mockFilter = m::mock(MapXmlFile::class);
        $mockFilter->shouldReceive('setMapping')
            ->once()
            ->with($mockSpec);

        $mockParser = m::mock(ParseXmlString::class);

        $mockXsdValidator = m::mock(Xsd::class);
        $mockXsdValidator->shouldReceive('setXsd')
            ->once()
            ->with(TransExchangeClientFactory::PUBLISH_XSD);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn(['ebsr' => $config]);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('TransExchangePublisherXmlMapping')->andReturn($mockSpec);
        $mockSl->shouldReceive('get')->with(MapXmlFile::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(ParseXmlString::class)->andReturn($mockParser);
        $mockSl->shouldReceive('get')->with(Xsd::class)->andReturn($mockXsdValidator);

        $sut = new TransExchangeClientFactory();
        $service = $sut->__invoke($mockSl, TransExchangeClient::class);

        $this->assertInstanceOf(TransExchangeClient::class, $service);
    }
}
