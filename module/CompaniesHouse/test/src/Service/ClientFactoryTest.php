<?php

namespace Dvsa\Olcs\CompaniesHouse\Test\Service;

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
    /** @var  \Laminas\ServiceManager\ServiceLocatorInterface|m\MockInterface */
    private $sl;

    public function setUp(): void
    {
        $this->sl = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class)->makePartial();

        $this->sut = new ClientFactory();
    }

    public function testOptionsMissing()
    {
        //  expect
        $this->expectException(\RuntimeException::class);

        //  call
        $this->sl->shouldReceive('get')->with('Configuration')->once()->andReturn([]);

        $this->sut->__invoke($this->sl, Client::class, null);
    }

    public function testOptionsBaseUriMissing()
    {
        //  expect
        $this->expectException(\RuntimeException::class, 'Missing required option companies_house.client.baseuri');

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

        $this->sut->__invoke($this->sl, Client::class, null);
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

        $service = $this->sut->__invoke($this->sl, Client::class, null);

        static::assertInstanceOf(Client::class, $service);
    }
}
