<?php

/**
 * UserListInternalTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\User\UserListInternal as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Transfer\Query\User\UserList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * UserListInternalTest
 */
class UserListInternalTest extends QueryHandlerTestCase
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

    public function testHandleQuery()
    {
        $user = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $user->setId(74);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::INTERNAL_USER, null)
            ->andReturn(true);

        $this->repoMap['User']->shouldReceive('fetchList')->andReturn([$user]);
        $this->repoMap['User']->shouldReceive('fetchCount')->andReturn('COUNT');

        $query = Query::create(['QUERY']);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(74, $result['result'][0]['id']);
        $this->assertSame('COUNT', $result['count']);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testHandleQueryThrowsIncorrectPermissionException()
    {
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::INTERNAL_USER, null)
            ->andReturn(false);

        $query = Query::create(['QUERY']);

        $this->sut->handleQuery($query);
    }
}
