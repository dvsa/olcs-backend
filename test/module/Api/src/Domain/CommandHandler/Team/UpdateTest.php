<?php

/**
 * Update Team Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Team;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Team\UpdateTeam as UpdateTeam;
use Dvsa\Olcs\Api\Domain\Repository\Team as TeamRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Team\UpdateTeam as Cmd;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Update Team Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateTeam();
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
                'id' => 1,
                'version' => 2,
                'name' => 'foo',
                'description' => 'bar',
                'trafficArea' => 5
            ]
        );

        $mockTeam = m::mock(TeamEntity::class)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->shouldReceive('setName')
            ->with('foo')
            ->once()
            ->shouldReceive('setDescription')
            ->with('bar')
            ->once()
            ->shouldReceive('setTrafficArea')
            ->with($this->references[TrafficAreaEntity::class][5])
            ->getMock();

        $this->repoMap['Team']
            ->shouldReceive('fetchByName')
            ->with('foo')
            ->once()
            ->andReturn([$mockTeam])
            ->shouldReceive('fetchById')
            ->with(1, \Doctrine\ORM\Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockTeam)
            ->shouldReceive('save')
            ->with($mockTeam)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $res = $result->toArray();
        $this->assertEquals(1, $res['id']['team']);
        $this->assertEquals(['Team updated successfully'], $res['messages']);
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
                'id' => 1,
                'name' => 'foo',
                'description' => 'bar',
                'trafficArea' => 5
            ]
        );

        $mockTeam = m::mock()
            ->shouldReceive('getId')
            ->andReturn(2)
            ->getMock();

        $this->repoMap['Team']
            ->shouldReceive('fetchByName')
            ->with('foo')
            ->once()
            ->andReturn([$mockTeam])
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithForbiddenException()
    {
        $this->setExpectedException(ForbiddenException::class);

        $command = Cmd::create(
            [
                'id' => 1,
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
