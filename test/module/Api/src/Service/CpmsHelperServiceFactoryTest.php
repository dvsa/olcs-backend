<?php

/**
 * CPMS Helper Service Factory Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CpmsHelperServiceFactory as Sut;
use Dvsa\Olcs\Api\Service\CpmsV1HelperService as V1;
use Dvsa\Olcs\Api\Service\CpmsV2HelperService as V2;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CPMS Helper Service Factory Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsHelperServiceFactoryTest extends MockeryTestCase
{
    /**
     * @param array $config
     * @param string $expectedServiceClass
     * @param boolean whether FeesHelperService should be requested via service locator
     * @dataProvider provider
     */
    public function testCreateService($config, $expectedServiceClass, $requiresFeesHelper)
    {
        $mockCpmsClient = m::mock()
            ->shouldReceive('getOptions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDomain')
                    ->andReturn('fake-domain')
                    ->getMock()
            )
            ->getMock();

        $mockFeesHelper = m::mock();

        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm
            ->shouldReceive('get')
            ->with('cpms\service\api')
            ->once()
            ->andReturn($mockCpmsClient)
            ->shouldReceive('get')
            ->with('Config')
            ->once()
            ->andReturn($config)
            ->shouldReceive('get')
            ->with('FeesHelperService')
            ->times($requiresFeesHelper ? 1 : 0)
            ->andReturn($mockFeesHelper);

        $sut = new Sut();

        $result = $sut->createService($sm);

        $this->assertInstanceOf($expectedServiceClass, $result);
    }

    public function provider()
    {
        return [
            'missing config' => [
                [],
                V1::class,
                false,
            ],
            'v1 config string' => [
                [
                    'cpms_api' => [
                        'rest_client' => [
                            'options' => [
                                'version' => '1',
                            ],
                        ],
                    ],
                ],
                V1::class,
                false,
            ],
            'v2 config string' => [
                [
                    'cpms_api' => [
                        'rest_client' => [
                            'options' => [
                                'version' => '2',
                            ],
                        ],
                    ],
                ],
                V2::class,
                true,
            ],
            'v1 config int' => [
                [
                    'cpms_api' => [
                        'rest_client' => [
                            'options' => [
                                'version' => 1,
                            ],
                        ],
                    ],
                ],
                V1::class,
                false,
            ],
            'v2 config int' => [
                [
                    'cpms_api' => [
                        'rest_client' => [
                            'options' => [
                                'version' => 2,
                            ],
                        ],
                    ],
                ],
                V2::class,
                true,
            ],
        ];
    }
}
