<?php

namespace Dvsa\OlcsTest\Api\Service\Data;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Nysiis\NysiisRestClientFactory;
use Dvsa\Olcs\Api\Service\Nysiis\NysiisRestClient;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * NysiisRestClientFactory Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class NysiisRestClientFactoryTest extends MockeryTestCase
{
    /**
     * Tests client created properly
     */
    public function testCreateServiceValid()
    {
        $config = [
            'nysiis' => [
                'rest' => [
                    'uri' => 'address',
                    'options' => []
                ]
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisRestClientFactory();
        $this->assertInstanceOf(NysiisRestClient::class, $sut->createService($mockSl));
    }

    /**
     * Tests exception thrown for missing config
     *
     * @param $config
     * @param $errorMsg
     *
     * @dataProvider createServiceFailProvider
     */
    public function testCreateServiceMissingConfig($config, $errorMsg)
    {
        $this->expectException(\RuntimeException::class, $errorMsg);

        $config = [
            'nysiis' => [
                'rest' => $config
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new NysiisRestClientFactory();
        $sut->createService($mockSl);
    }

    /**
     * data provider for testing service creation failures
     */
    public function createServiceFailProvider()
    {
        return [
            [['uri' => 'address'], 'Missing nysiis rest client options'],
            [['options' => []], 'Missing nysiis rest client uri']
        ];
    }
}
