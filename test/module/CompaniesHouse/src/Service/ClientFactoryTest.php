<?php

namespace Dvsa\OlcsTest\CompaniesHouse\Service;

use Dvsa\Olcs\CompaniesHouse\Service\Client;
use Dvsa\Olcs\CompaniesHouse\Service\ClientFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ClientFactoryTest
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ClientFactoryTest extends MockeryTestCase
{
    /** @var  ClientFactory */
    protected $sut;
    /** @var  \Zend\ServiceManager\ServiceLocatorInterface|m\MockInterface */
    private $sl;

    public function setUp()
    {
        $this->sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);

        $this->sut = new ClientFactory();
    }

    public function testOptionsMissing()
    {
        //  expect
        $this->setExpectedException(\RuntimeException::class);

        //  call
        $this->sl->shouldReceive('get')->with('Configuration')->once()->andReturn([]);

        $this->sut->createService($this->sl);
    }

    public function testOptionsBaseUriMissing()
    {
        //  expect
        $this->setExpectedException(\RuntimeException::class, 'Missing required option companies_house.client.baseuri');

        //  call
        $this->sl->shouldReceive('get')->with('Configuration')->once()->andReturn(
            [
                'companies_house' => [
                    'http' => [],
                    'client' => [],
                    'auth' => []
                ]
            ]
        );

        $this->sut->createService($this->sl);
    }

    public function testOptions()
    {
        $this->sl->shouldReceive('get')->with('Configuration')->once()->andReturn(
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

        $service = $this->sut->createService($this->sl);

        static::assertInstanceOf(Client::class, $service);
    }
}
