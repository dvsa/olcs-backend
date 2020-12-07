<?php

/**
 * Client Factory Test
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Dvsa\Olcs\Api\Service\OpenAm\ClientFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Client Factory Test
 */
class ClientFactoryTest extends MockeryTestCase
{
    /**
     * @dataProvider provideCreateServiceExceptions
     * @param $config
     * @param $expectedMessage
     */
    public function testCreateServiceExceptions($config, $expectedMessage)
    {
        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn(['openam' => $config]);

        $sut = new ClientFactory();
        $passed = false;

        try {
            $sut->createService($mockSl);
        } catch (\Exception $e) {
            if ($e->getMessage() === $expectedMessage) {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception not thrown');
    }

    public function provideCreateServiceExceptions()
    {
        return [
            [[], 'Cannot create service, config for open am api credentials is missing'],
            [['username' => 'asdfg'], 'Cannot create service, config for open am api credentials is missing'],
            [['password' => 'asdfg'], 'Cannot create service, config for open am api credentials is missing'],
            [
                ['password' => 'asdfg', 'username' => 'asdfg'],
                'Cannot create service, config for open am api uri is missing'
            ]
        ];
    }

    public function testCreateService()
    {
        $config = [
            'openam' =>  [
                'http_client_options' => [],
                'password' => 'asdfg',
                'username' => 'asdfg',
                'uri' => 'http://openam.com:12345'
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new ClientFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Client::class, $service);
    }
}
