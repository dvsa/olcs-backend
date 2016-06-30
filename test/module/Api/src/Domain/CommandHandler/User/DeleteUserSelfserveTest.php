<?php

/**
 * Delete User Selfserve Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\DeleteUserSelfserve as Sut;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Transfer\Command\User\DeleteUserSelfserve as Cmd;

/**
 * Class Delete User Selfserve Test
 */
class DeleteUserSelfserveTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('User', UserRepo::class);
        $this->mockRepo('Task', TaskRepo::class);

        $this->mockedSmServices = [
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

        $userEntity = m::mock(UserEntity::class)->makePartial();
        $userEntity->setId(1);
        $userEntity->setPid('pid');

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->once()
            ->with('pid');

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

        $userEntity = m::mock(UserEntity::class)->makePartial();
        $userEntity->setId(1);
        $userEntity->setLoginId('login_id');

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->never();

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
}
