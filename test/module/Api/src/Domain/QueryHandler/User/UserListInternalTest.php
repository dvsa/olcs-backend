<?php

/**
 * UserListInternalTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\User\UserListInternal as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Transfer\Query\User\UserListInternal as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * UserListInternalTest
 */
class UserListInternalTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $user = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $user->setId(74);

        $this->repoMap['User']->shouldReceive('fetchList')->andReturn([$user]);
        $this->repoMap['User']->shouldReceive('fetchCount')->andReturn('COUNT');

        $query = Query::create(['QUERY']);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(74, $result['result'][0]['id']);
        $this->assertSame('COUNT', $result['count']);
    }
}
