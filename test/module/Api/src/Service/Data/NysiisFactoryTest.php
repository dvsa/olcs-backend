<?php

/**
 * NysiisFactory Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Data;

use Dvsa\Olcs\Api\Service\Data\NysiisFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Soap\Client as ZendSoapClient;

/**
 * NysiisFactory Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class NysiisFactoryTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');

        parent::setUp();
    }

    public function testCreateServiceValidSoapClient()
    {
        $config = [
            'nysiis' => [
                'wsdl' => [
                    'uri' => 'wsdlFile'
                ]
            ]
        ];
        $this->sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $mockSoapClient = m::mock(\SoapClient::class);

        $nysiisFactory = m::mock('Dvsa\Olcs\Api\Service\Data\NysiisFactory')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $nysiisFactory->shouldReceive('generateSoapClient')->with($config)->andReturn($mockSoapClient);

        $service = $nysiisFactory->createService($this->sm);

        $this->assertEquals($mockSoapClient, $service->getSoapClient());
        $this->assertEquals($config, $service->getNysiisConfig());
    }

    public function testCreateServiceExceptionLogged()
    {
        $config = [
            'nysiis' => [
                'wsdl' => [
                    'uri' => 'wsdlFile'
                ]
            ]
        ];
        $this->sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $sut = new NysiisFactory();

        $this->setExpectedException('SoapFault');

        $service = $sut->createService($this->sm);

        $this->assertFalse($service->getSoapClient());
        $this->assertEquals($config, $service->getNysiisConfig());
    }

    public function testCreateServiceWithNoWsdlExceptionLogged()
    {
        $config = [
            'nysiis' => [
                'wsdl' => []
            ]
        ];
        $this->sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $sut = new NysiisFactory();

        $this->setExpectedException('\Dvsa\Olcs\Api\Domain\Exception\NysiisException');

        $service = $sut->createService($this->sm);

        $this->assertFalse($service->getSoapClient());
        $this->assertEquals($config, $service->getNysiisConfig());
    }
}
