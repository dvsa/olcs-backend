<?php

/**
 * OlcsBlameableListener Test
 */
namespace Dvsa\OlcsTest\Api\Mvc;

use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Mvc\OlcsBlameableListener;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * OlcsBlameableListener Test
 */
class OlcsBlameableListenerTest extends MockeryTestCase
{
    /**
     * @dataProvider getUserValueDataProvider
     *
     */
    public function testGetUserValue($currentUser, $expected)
    {
        /** @var AuthorizationService $mockAuth */
        $mockAuth = m::mock(AuthorizationService::class);
        $mockAuth->shouldReceive('getIdentity->getUser')->andReturn($currentUser);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with(AuthorizationService::class)->andReturn($mockAuth);

        $sut = new OlcsBlameableListener();
        $sut->setServiceLocator($mockSl);

        $this->assertSame($expected, $sut->getUserValue(null, null));
    }

    public function getUserValueDataProvider()
    {
        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setPid('abc');

        return [
            [$mockUser, $mockUser],
            [User::anon(), null],
            [null, null],
        ];
    }
}
