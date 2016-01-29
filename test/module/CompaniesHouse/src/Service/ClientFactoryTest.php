<?php

namespace Dvsa\OlcsTest\CompaniesHouse\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\CompaniesHouse\Service\ClientFactory;

/**
 * ClientFactoryTest
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ClientFactoryTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ClientFactory();
    }

    public function testOptionsMissing()
    {
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('Configuration')->once()->andReturn([]);

        $this->setExpectedException(\RuntimeException::class);
        $this->sut->createService($sl);
    }

    public function testOptionsBaseUriMissing()
    {
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('Configuration')->once()->andReturn(
            [
                'companies_house' => [
                    'http' => [],
                    'client' => [],
                    'auth' => []
                ]
            ]
        );

        $this->setExpectedException(\RuntimeException::class, 'Missing required option companies_house.client.baseuri');
        $this->sut->createService($sl);
    }

    public function testOptions()
    {
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('Configuration')->once()->andReturn(
            [
                'companies_house' => [
                    'http' => [
                        'foo' => 'bar',
                    ],
                    'client' => [
                        'baseuri' => 'http://companies-house-api/',
                    ],
                    'auth' => [
                        'username' => 'user',
                        'password' => 'secret',
                    ]
                ]
            ]
        );

        $service = $this->sut->createService($sl);

        $this->assertSame('http://companies-house-api', $service->getBaseUri());
        // HttpClient doesn't expose options so we can't assert they were set :(
    }
}
