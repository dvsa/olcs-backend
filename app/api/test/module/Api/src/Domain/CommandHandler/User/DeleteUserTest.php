<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepo;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationUser as OrganisationUserRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\DeleteUser as Sut;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Transfer\Command\User\DeleteUser as Cmd;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class Delete User Test
 */
class DeleteUserTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('User', UserRepo::class);
        $this->mockRepo('Task', TaskRepo::class);
        $this->mockRepo('OrganisationUser', OrganisationUserRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            UserInterface::class => m::mock(UserInterface::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $userId = 1;

        $data = [
            'id' => $userId
        ];
        $tasks = [];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->once()
            ->with('pid');

        $userEntity = m::mock(UserEntity::class)->makePartial();
        $userEntity->setId(1);
        $userEntity->setPid('pid');

        $this->repoMap['User']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->andReturn($userEntity)
            ->shouldReceive('delete')
            ->once();

        $this->repoMap['Task']
            ->shouldReceive('fetchByUser')
            ->with($userId, true)
            ->once()
            ->andReturn($tasks);

        $this->repoMap['OrganisationUser']
            ->shouldReceive('deleteByUserId')
            ->with($userId)
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => $userId
            ],
            'messages' => [
                'User deleted successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testHandleCommandForUserWithTasks()
    {
        $userId = 1;

        $data = [
            'id' => $userId
        ];

        $tasks = [
            ['id' => 100]
        ];

        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(true);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->never();

        $userEntity = m::mock(UserEntity::class)->makePartial();
        $userEntity->setId(1);
        $userEntity->setLoginId('login_id');

        $this->repoMap['User']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->andReturn($userEntity)
            ->shouldReceive('delete')
            ->never();

        $this->repoMap['Task']
            ->shouldReceive('fetchByUser')
            ->with($userId, true)
            ->once()
            ->andReturn($tasks);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testHandleCommandThrowsIncorrectPermissionException()
    {
        $data = [
            'id' => 111,
            'version' => 1,
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn(false);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->never();

        $this->repoMap['User']
            ->shouldReceive('fetchUsingId')
            ->never()
            ->shouldReceive('delete')
            ->never();

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }
}
