<?php

/**
 * Create Team Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Team;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Team\CreateTeam as CreateTeam;
use Dvsa\Olcs\Api\Domain\Repository\Team as TeamRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Team\CreateTeam as Cmd;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Create Team Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateTeam();
        $this->mockRepo('Team', TeamRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];
        $this->mockAuthService();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            TrafficAreaEntity::class => [
                5 => m::mock(TrafficAreaEntity::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(Permission::CAN_MANAGE_USER_INTERNAL, null)
            ->once()
            ->andReturn(true);

        $command = Cmd::create(
            [
                'name' => 'foo',
                'description' => 'bar',
                'trafficArea' => 5
            ]
        );

        $team = null;
        $this->repoMap['Team']
            ->shouldReceive('fetchByName')
            ->with('foo')
            ->once()
            ->andReturn([])
            ->shouldReceive('save')
            ->once()
            ->with(m::type(TeamEntity::class))
            ->andReturnUsing(
                function (TeamEntity $tm) use (&$team) {
                    $tm->setId(111);
                    $team = $tm;
                }
            )
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['team']);
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

    public function testHandleCommandWithVaidationException()
    {
        $this->setExpectedException(ValidationException::class);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(Permission::CAN_MANAGE_USER_INTERNAL, null)
            ->once()
            ->andReturn(true);

        $command = Cmd::create(
            [
                'name' => 'foo',
                'description' => 'bar',
                'trafficArea' => 5
            ]
        );

        $this->repoMap['Team']
            ->shouldReceive('fetchByName')
            ->with('foo')
            ->once()
            ->andReturn(['foo'])
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithForbiddenException()
    {
        $this->setExpectedException(ForbiddenException::class);

        $command = Cmd::create(
            [
                'name' => 'foo',
                'description' => 'bar',
                'trafficArea' => 5
            ]
        );

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(Permission::CAN_MANAGE_USER_INTERNAL, null)
            ->once()
            ->andReturn(false);

        $this->sut->handleCommand($command);
    }
}
