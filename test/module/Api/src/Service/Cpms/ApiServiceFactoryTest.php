<?php

namespace Dvsa\OlcsTest\Api\Service\Cpms;

use Dvsa\Olcs\Api\Service\Cpms\ApiServiceFactory;
use Dvsa\Olcs\Cpms\Service\ApiService;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use LmcRbacMvc\Service\AuthorizationService;

class ApiServiceFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $config = [
            'cpms_api' => [
                'rest_client' => [
                    'options' => [
                        'version' => 2,
                        'domain' => 'api.cpms.domain',
                        'client_id' => 'some-client-id',
                        'client_secret' => 'some-secret',
                        'customer_reference' => 'some-customer-ref',
                        'grant_type' => 'client_credentials',
                        'timeout' => 15.0,
                        'headers' => [
                            'Accept' => 'application/json',
                        ],

                    ],
                ],
            ],
            'log' => [
                'Logger' => [
                    'writers' => [
                        'full' => [
                            'options' => [
                                'stream' => '/var/tmp/backend.log',
                                'filters' => [
                                    'priority' => [
                                        'name' => 'priority',
                                        'options' => [
                                            'priority' => 1,
                                        ]
                                    ],
                                ]
                            ],
                        ]
                    ]
                ],
            ],
            'cpms_credentials' => [
                'client_id' => 'a-client-id',
                'client_secret' => 'a-client-secret',
            ],
        ];

        $mockSl = m::mock(ContainerInterface::class);

        $mockAuth = m::mock(AuthorizationService::class);
        $mockAuth->
        shouldReceive('getIdentity->getUser->getId')->andReturn('2');

            $mockSl
                ->shouldReceive('get')
                ->with('Config')
                ->andReturn($config)
                ->shouldReceive('get')
                ->with(AuthorizationService::class)
                ->andReturn($mockAuth)
                ->getMock();

            $sut = new ApiServiceFactory();

        $this->assertInstanceOf(ApiService::class, $sut->__invoke($mockSl, ApiService::class));
    }
}
