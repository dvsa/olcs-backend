<?php

/**
 * User Factory Test
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\ClientInterface;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Dvsa\Olcs\Api\Service\OpenAm\UserFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * User Factory Test
 */
class UserFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockClient = m::mock(ClientInterface::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with(ClientInterface::class)->andReturn($mockClient);

        $sut = new UserFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(User::class, $service);
    }
}
