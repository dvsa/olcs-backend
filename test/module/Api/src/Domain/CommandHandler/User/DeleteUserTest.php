<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException;
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
use Zend\Http\Response;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class Delete User Test
 */
class DeleteUserTest extends CommandHandlerTestCase
{
    /** @var UserEntity|m\Mock */
    private $userEntity;

    public function setUp(): void
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

        $this->userEntity = m::mock(UserEntity::class)->makePartial();
        $this->userEntity->setId('DUMMY-USER-ID');
        $this->userEntity->setPid('DUMMY-USER-PID');
        $this->userEntity->setLoginId('login_id');

        $this->repoMap['User']
            ->shouldReceive('fetchUsingId')
            ->andReturn($this->userEntity);
    }

    public function testHandleCommandThrowsIncorrectPermissionException()
    {
        $this->setPermissionGranted(false);

        $this->expectException(ForbiddenException::class);
        $this->sut->handleCommand(Cmd::create(['id' => 111, 'version' => 1]));
    }

    public function testHandleCommandForUserWithTasks()
    {
        $this->setPermissionGranted(true);
        $this->setUserTasks([['id' => 100]]);

        $this->expectException(BadRequestException::class);
        $this->sut->handleCommand(Cmd::create(['id' => 'DUMMY-USER-ID']));
    }

    public function testThatCommandHandlerIsTransactional()
    {
        $this->assertInstanceOf(TransactionedInterface::class, $this->sut);
    }

    public function testHandleCommandWhenOpenAmError()
    {
        $this->setPermissionGranted(true);
        $this->setUserTasks([]);
        $this->openAmShouldThrow(500);

        $this->expectDeleteUser();
        $this->expectDeleteOrganisationUser();

        $this->expectException(FailedRequestException::class);
        $this->sut->handleCommand(Cmd::create(['id' => 'DUMMY-USER-ID']));
    }

    public function testHandleCommandWhenOpenAmUserDoesNotExist()
    {
        $this->setPermissionGranted(true);
        $this->setUserTasks([]);
        $this->openAmShouldThrow(404);

        $this->expectDeleteUser();
        $this->expectDeleteOrganisationUser();

        $this->assertEquals(
            ['id' => ['user' => 'DUMMY-USER-ID'], 'messages' => ['User deleted successfully']],
            $this->sut->handleCommand(Cmd::create(['id' => 'DUMMY-USER-ID']))->toArray()
        );
    }

    public function testHandleCommand()
    {
        $this->setPermissionGranted(true);
        $this->setUserTasks([]);

        $this->expectDeleteUser();
        $this->expectDeleteOrganisationUser();
        $this->expectDisableOpenAmUser();

        $this->assertEquals(
            ['id' => ['user' => 'DUMMY-USER-ID'], 'messages' => ['User deleted successfully']],
            $this->sut->handleCommand(Cmd::create(['id' => 'DUMMY-USER-ID']))->toArray()
        );
    }

    private function setPermissionGranted($granted)
    {
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(PermissionEntity::CAN_MANAGE_USER_INTERNAL, null)
            ->andReturn($granted);
    }

    private function setUserTasks($tasks)
    {
        $this->repoMap['Task']
            ->shouldReceive('fetchByUser')
            ->with('DUMMY-USER-ID', true)
            ->andReturn($tasks);
    }

    private function expectDisableOpenAmUser()
    {
        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->with('DUMMY-USER-PID')
            ->once();
    }

    private function expectDeleteUser()
    {
        $this->repoMap['User']
            ->shouldReceive('delete')
            ->with($this->userEntity)
            ->once();
    }

    private function expectDeleteOrganisationUser()
    {
        $this->repoMap['OrganisationUser']
            ->shouldReceive('deleteByUserId')
            ->with('DUMMY-USER-ID')
            ->once();
    }

    protected function openAmShouldThrow($code)
    {
        $response = new Response();
        $response->setStatusCode($code);
        $this->mockedSmServices[UserInterface::class]->shouldReceive('disableUser')
            ->andThrow(new FailedRequestException($response));
    }
}
