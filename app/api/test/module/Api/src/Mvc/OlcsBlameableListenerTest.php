<?php

namespace Dvsa\OlcsTest\Api\Mvc;

use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Mvc\OlcsBlameableListener;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * OlcsBlameableListener Test
 */
class OlcsBlameableListenerTest extends MockeryTestCase
{
    /**
     * @dataProvider getUserValueDataProvider
     */
    public function testGetUserValue($currentUser, $expected)
    {
        /** @var AuthorizationService $mockAuth */
        $mockAuth = m::mock(AuthorizationService::class);
        $mockAuth->shouldReceive('getIdentity->getUser')->andReturn($currentUser);

        $mockUserRepo = m::mock(\Dvsa\Olcs\Api\Domain\Repository\User::class);

        $mockSl = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with(AuthorizationService::class)
            ->andReturn($mockAuth)
            ->once()
            ->shouldReceive('get')
            ->with('RepositoryServiceManager')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('User')
                ->andReturn($mockUserRepo)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('get')
            ->with(PidIdentityProvider::class)
            ->andReturn(
                m::mock()
                ->shouldReceive('getMasqueradedAsSystemUser')
                ->andReturn(false)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $sut = new OlcsBlameableListener();
        $sut->setServiceLocator($mockSl);

        $this->assertSame($expected, $sut->getUserValue(null, null));
    }

    public function testGetUserValueMasqueraded()
    {
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setPid('abc');

        /** @var AuthorizationService $mockAuth */
        $mockAuth = m::mock(AuthorizationService::class);

        $mockUserRepo = m::mock(\Dvsa\Olcs\Api\Domain\Repository\User::class)
            ->shouldReceive('fetchById')
            ->with(PidIdentityProvider::SYSTEM_USER)
            ->andReturn($mockUser)
            ->once()
            ->getMock();

        $mockSl = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with(AuthorizationService::class)
            ->andReturn($mockAuth)
            ->once()
            ->shouldReceive('get')
            ->with('RepositoryServiceManager')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('User')
                    ->andReturn($mockUserRepo)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('get')
            ->with(PidIdentityProvider::class)
            ->andReturn(
                m::mock()
                    ->shouldReceive('getMasqueradedAsSystemUser')
                    ->andReturn(true)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $sut = new OlcsBlameableListener();
        $sut->setServiceLocator($mockSl);

        $this->assertSame($mockUser, $sut->getUserValue(null, null));
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
