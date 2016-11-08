<?php

/**
 * NysiisFactory Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Nysiis\NysiisSoapClientFactory;
use Dvsa\Olcs\Api\Service\Nysiis\NysiisSoapClient;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Soap\Client as ZendSoapClient;

/**
 * NysiisSoapClientFactory Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class NysiisSoapClientFactoryTest extends MockeryTestCase
{
    /**
     * Tests client created properly
     */
    public function testCreateServiceValidSoapClient()
    {
        $config = [
            'nysiis' => [
                'wsdl' => 'wsdl address',
                'options' => []
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisSoapClientFactory();
        $this->assertInstanceOf(NysiisSoapClient::class, $sut->createService($mockSl));
    }

    /**
     * Tests exception thrown for missing wsdl file
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NysiisException
     * @expectedExceptionMessage No NYSIIS Wsdl file specified in config
     */
    public function testCreateServiceMissingWsdl()
    {
        $config = [
            'nysiis' => [
                'options' => []
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisSoapClientFactory();
        $sut->createService($mockSl);
    }

    /**
     * Tests exception thrown for missing config
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NysiisException
     * @expectedExceptionMessage No NYSIIS options specified in config
     */
    public function testCreateServiceMissingOptions()
    {
        $config = [
            'nysiis' => [
                'wsdl' => '/path/to/wsdl'
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisSoapClientFactory();
        $sut->createService($mockSl);
    }

    /**
     * Tests exception thrown for invalid option
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NysiisException
     * @expectedExceptionMessage Unknown SOAP client option
     */
    public function testCreateServiceInvalidOption()
    {
        $config = [
            'nysiis' => [
                'wsdl' => '/path/to/wsdl',
                'options' => [
                    'invalid_option' => 'invalid_option'
                ]
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisSoapClientFactory();
        $sut->createService($mockSl);
    }

    /**
     * Tests exception thrown for invalid https certificate
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NysiisException
     * @expectedExceptionMessage Invalid HTTPS client certificate path.
     */
    public function testCreateServiceInvalidCert()
    {
        $config = [
            'nysiis' => [
                'wsdl' => '/path/to/wsdl',
                'options' => [
                    'local_cert' => '/invalid/cert/path/cert.pem'
                ]
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisSoapClientFactory();
        $sut->createService($mockSl);
    }
}
