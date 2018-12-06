<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\CommandHandler\User\DeleteUserSelfserve;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Transfer\Command\User\DeleteUserSelfserve as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class Delete User Selfserve Test
 */
class DeleteUserSelfserveTest extends CommandHandlerTestCase
{
    const USER_ID = 8888;
    const ADMIN_USER_ID = 8880;

    /** @var  DeleteUserSelfserve */
    protected $sut;
    /** @var  m\MockInterface */
    private $mockAuth;

    public function setUp()
    {
        $this->sut = new DeleteUserSelfserve();
        $this->mockRepo('User', Repository\User::class);
        $this->mockRepo('Task', Repository\Task::class);
        $this->mockRepo('OrganisationUser', Repository\OrganisationUser::class);

        $this->mockAuth = m::mock(AuthorizationService::class);
        $this->sut->setAuthService($this->mockAuth);

        $this->mockedSmServices = [
            UserInterface::class => m::mock(UserInterface::class),
            AuthorizationService::class => $this->mockAuth,
        ];

        $adminUserEntity = (new UserEntity(9999, new RefData()))
            ->setId(self::ADMIN_USER_ID);
        $this->mockAuth->shouldReceive('getIdentity->getUser')->once()->andReturn($adminUserEntity);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => self::USER_ID,
        ];
        $tasks = [];

        $command = Cmd::create($data);

        $userEntity = new UserEntity(9999, new RefData());
        $userEntity->setId(self::USER_ID);
        $userEntity->setPid('pid');

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')->once()->with('pid');

        $this->repoMap['User']
            ->shouldReceive('fetchUsingId')->once()->andReturn($userEntity)
            ->shouldReceive('delete')->once();

        $this->repoMap['Task']
            ->shouldReceive('fetchByUser')->with(self::USER_ID, true)->once()->andReturn($tasks);

        $this->repoMap['OrganisationUser']
            ->shouldReceive('deleteByUserId')->with(self::USER_ID)->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'user' => self::USER_ID,
            ],
            'messages' => [
                'User deleted successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandDeleteHimself()
    {
        $this->setExpectedException(BadRequestException::class, 'You can not delete yourself');

        $command = Cmd::create(
            [
                'id' => self::ADMIN_USER_ID,
            ]
        );

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testHandleCommandForUserWithTasks()
    {
        $data = [
            'id' => self::USER_ID,
        ];
        $command = Cmd::create($data);

        $userEntity = new UserEntity(9999, new RefData());
        $userEntity->setId(self::USER_ID);
        $userEntity->setLoginId('login_id');

        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')->never();

        $this->repoMap['User']
            ->shouldReceive('fetchUsingId')->once()->andReturn($userEntity)
            ->shouldReceive('delete')->never();

        $tasks = [
            ['id' => 100]
        ];

        $this->repoMap['Task']
            ->shouldReceive('fetchByUser')->with(self::USER_ID, true)->once()->andReturn($tasks);

        $this->sut->handleCommand($command);
    }
}
