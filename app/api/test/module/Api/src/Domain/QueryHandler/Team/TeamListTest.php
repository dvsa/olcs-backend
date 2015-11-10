<?php

/**
 * Team List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Team;

use Dvsa\Olcs\Api\Domain\QueryHandler\Team\TeamList as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Team\TeamList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Team as TeamRepo;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * Team List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TeamListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Team', TeamRepo::class);
        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];
        $this->mockAuthService();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1]);

        $mockTeam = m::mock();
        $mockTeam->shouldReceive('serialize')->once()->andReturn('foo');

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(Permission::CAN_MANAGE_USER_INTERNAL, null)
            ->once()
            ->andReturn(true);

        $this->repoMap['Team']
            ->shouldReceive('fetchList')
            ->with($query, DoctrineQuery::HYDRATE_OBJECT)
            ->once()
            ->andReturn([$mockTeam])
            ->shouldReceive('fetchCount')
            ->with($query)
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->assertSame(
            [
                'result'    => ['foo'],
                'count'     => 1,
            ],
            $this->sut->handleQuery($query)
        );
    }

    public function testHandleQueryWithException()
    {
        $query = Query::create(['id' => 1]);

        $this->setExpectedException(ForbiddenException::class);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(Permission::CAN_MANAGE_USER_INTERNAL, null)
            ->once()
            ->andReturn(false);
        $this->sut->handleQuery($query);
    }

    protected function mockAuthService()
    {
        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);
    }
}
