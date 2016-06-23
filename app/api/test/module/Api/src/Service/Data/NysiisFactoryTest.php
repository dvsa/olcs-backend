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
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;

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

        $this->setExpectedException(NysiisException::class);

        $service = $sut->createService($this->sm);

        $this->assertFalse($service->getSoapClient());
        $this->assertEquals($config, $service->getNysiisConfig());
    }

    /**
     *
     */
    public function testCreateServiceValidWsdl()
    {
        $config = [
            'nysiis' => [
                'wsdl' => [
                    'uri' => __DIR__ . '/../../../../../../module/Nysiis/config/nysiis.svc.wsdl',
                    'soap' => [
                        'options' => []
                    ]
                ]
            ]
        ];

        $this->sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $sut = new NysiisFactory();

        $service = $sut->createService($this->sm);

        $this->assertinstanceOf(ZendSoapClient::class, $service->getSoapClient());
        $this->assertEquals($config, $service->getNysiisConfig());
    }
}
