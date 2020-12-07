<?php

namespace Dvsa\OlcsTest\Api\Mvc;

use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Mvc\OlcsBlameableListener;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepository;
use Gedmo\Mapping\Event\AdapterInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * OlcsBlameableListener Test
 */
class OlcsBlameableListenerTest extends MockeryTestCase
{
    /**
     * @dataProvider getFieldValueDataProvider
     */
    public function testGetFieldValue($currentUser, $expected)
    {
        /** @var AuthorizationService $mockAuth */
        $mockAuth = m::mock(AuthorizationService::class);
        $mockAuth->shouldReceive('getIdentity->getUser')->andReturn($currentUser);

        $mockUserRepo = m::mock(UserRepository::class);

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
                m::mock(PidIdentityProvider::class)
                ->shouldReceive('getMasqueradedAsSystemUser')
                ->andReturn(false)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $meta = new \stdClass;
        $field = 'field';
        $eventAdapter = m::mock(AdapterInterface::class);

        $sut = m::mock(OlcsBlameableListener::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $sut->setServiceLocator($mockSl);

        $sut->shouldReceive('setUserValue')
            ->with($expected)
            ->once()
            ->globally()
            ->ordered();
        $sut->shouldReceive('callParentGetFieldValue')
            ->with($meta, $field, $eventAdapter)
            ->once()
            ->andReturn($expected)
            ->globally()
            ->ordered();

        $this->assertSame(
            $expected,
            $sut->getFieldValue($meta, $field, $eventAdapter)
        );
    }

    public function testGetFieldValueMasqueraded()
    {
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setPid('abc');

        /** @var AuthorizationService $mockAuth */
        $mockAuth = m::mock(AuthorizationService::class);

        $mockUserRepo = m::mock(UserRepository::class)
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
                m::mock(PidIdentityProvider::class)
                    ->shouldReceive('getMasqueradedAsSystemUser')
                    ->andReturn(true)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $meta = new \stdClass;
        $field = 'field';
        $eventAdapter = m::mock(AdapterInterface::class);

        $sut = m::mock(OlcsBlameableListener::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $sut->setServiceLocator($mockSl);

        $sut->shouldReceive('setUserValue')
            ->with($mockUser)
            ->once()
            ->globally()
            ->ordered();
        $sut->shouldReceive('callParentGetFieldValue')
            ->with($meta, $field, $eventAdapter)
            ->once()
            ->andReturn($mockUser)
            ->globally()
            ->ordered();

        $this->assertSame(
            $mockUser,
            $sut->getFieldValue($meta, $field, $eventAdapter)
        );
    }

    public function getFieldValueDataProvider()
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
