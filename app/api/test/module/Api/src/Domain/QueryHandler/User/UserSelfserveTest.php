<?php

/**
 * UserSelfserveTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\User\UserSelfserve as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Query\User\UserSelfserve as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * UserSelfserveTest
 */
class UserSelfserveTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    /**
     * @dataProvider getHandleQueryDataProvider
     *
     */
    public function testHandleQuery($canManageUser, $canReadUser)
    {
        $mockUser = m::mock(UserEntity::class);
        $mockUser->shouldReceive('serialize')->once()->andReturn(['foo' => 'bar']);
        $mockUser->shouldReceive('getPermission')->once()->andReturn(UserEntity::PERMISSION_USER);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(PermissionEntity::CAN_MANAGE_USER_SELFSERVE, $mockUser)
            ->andReturn($canManageUser);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(PermissionEntity::CAN_READ_USER_SELFSERVE, $mockUser)
            ->andReturn($canReadUser);

        $query = Query::create(['QUERY']);

        $this->repoMap['User']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockUser);

        $result = $this->sut->handleQuery($query)->serialize();

        $this->assertSame(['foo' => 'bar', 'permission' => UserEntity::PERMISSION_USER], $result);
    }

    public function getHandleQueryDataProvider()
    {
        return [
            [true, true],
            [true, false],
            [false, true],
        ];
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testHandleQueryThrowsIncorrectPermissionException()
    {
        $mockUser = m::mock(UserEntity::class);
        $mockUser->shouldReceive('serialize')->never();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_SELFSERVE, $mockUser)
            ->andReturn(false);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_READ_USER_SELFSERVE, $mockUser)
            ->andReturn(false);

        $query = Query::create(['QUERY']);

        $this->repoMap['User']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockUser);

        $this->sut->handleQuery($query)->serialize();
    }
}
