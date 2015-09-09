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
use Dvsa\OlcsTest\Api\MockLoggerTrait;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CPMS Helper Service Factory Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsHelperServiceFactoryTest extends MockeryTestCase
{
    use MockLoggerTrait;

    /**
     * @dataProvider provider
     */
    public function testCreateService($config, $expectedServiceClass)
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

        $mockLogger = $this->mockLogger();

        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm
            ->shouldReceive('get')
            ->with('cpms\service\api')
            ->andReturn($mockCpmsClient)
            ->shouldReceive('get')
            ->with('Logger')
            ->andReturn($mockLogger)
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

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
            ],
            'v1 config string' => [
                [
                    'cpms_api' => [
                        'version' => '1',
                    ],
                ],
                V1::class,
            ],
            'v2 config string' => [
                [
                    'cpms_api' => [
                        'version' => '2',
                    ],
                ],
                V2::class,
            ],
            'v1 config int' => [
                [
                    'cpms_api' => [
                        'version' => 1,
                    ],
                ],
                V1::class,
            ],
            'v2 config int' => [
                [
                    'cpms_api' => [
                        'version' => 2,
                    ],
                ],
                V2::class,
            ],
        ];
    }
}
