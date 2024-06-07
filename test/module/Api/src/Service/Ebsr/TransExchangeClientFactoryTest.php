<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\AppRegistration\TransXChangeAppRegistrationService;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClientFactory;
use Psr\Container\ContainerInterface;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TransExchangeClientFactoryTest extends TestCase
{
    public function testInvokeNoConfig()
    {
        $this->expectException(\RuntimeException::class);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

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

        $mockTxcAppRegService = m::mock(TransXChangeAppRegistrationService::class);
        $mockTxcAppRegService->shouldReceive('getToken')->once()->andReturn('token');

        $mockToggle = m::mock(ToggleService::class);
        $mockToggle->shouldReceive('getToggleService')->andReturn($mockToggle);
        $mockToggle->shouldReceive('isEnabled')->with(FeatureToggle::BACKEND_TRANSXCHANGE)->andReturn(false);
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn(['ebsr' => $config]);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('TransExchangePublisherXmlMapping')->andReturn($mockSpec);
        $mockSl->shouldReceive('get')->with(MapXmlFile::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(ParseXmlString::class)->andReturn($mockParser);
        $mockSl->shouldReceive('get')->with(Xsd::class)->andReturn($mockXsdValidator);
        $mockSl->shouldReceive('get')->with(ToggleService::class)->andReturn($mockToggle);
        $mockSl->shouldReceive('get')->with(TransXChangeAppRegistrationService::class)->andReturn($mockTxcAppRegService);
        $sut = new TransExchangeClientFactory();
        $service = $sut->__invoke($mockSl, TransExchangeClient::class);

        $this->assertInstanceOf(TransExchangeClient::class, $service);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreateServiceToggleOn()
    {
        $config = [
            'transexchange_publisher' => [
                'uri' => 'http://localhost:8080/txc/',
                'new_uri' => 'http://txc.dev/',
                'options' => ['argseparator' => '~'],
                'template_file' => 'template.xml'
            ]
        ];
        $mockToggle = m::mock(ToggleService::class);
        $mockToggle->shouldReceive('getToggleService')->andReturn($mockToggle);
        $mockToggle->shouldReceive('isEnabled')->with(FeatureToggle::BACKEND_TRANSXCHANGE)->andReturn(true);

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
        $mockTxcAppRegService = m::mock(TransXChangeAppRegistrationService::class);
        $mockTxcAppRegService->shouldReceive('getToken')->once()->andReturn('token');
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn(['ebsr' => $config]);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('TransExchangePublisherXmlMapping')->andReturn($mockSpec);
        $mockSl->shouldReceive('get')->with(MapXmlFile::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(ParseXmlString::class)->andReturn($mockParser);
        $mockSl->shouldReceive('get')->with(Xsd::class)->andReturn($mockXsdValidator);
        $mockSl->shouldReceive('get')->with(ToggleService::class)->andReturn($mockToggle);
        $mockSl->shouldReceive('get')->with(TransXChangeAppRegistrationService::class)->andReturn($mockTxcAppRegService);
        $sut = new TransExchangeClientFactory();
        $service = $sut->__invoke($mockSl, TransExchangeClient::class);

        $this->assertInstanceOf(TransExchangeClient::class, $service);
    }
}
