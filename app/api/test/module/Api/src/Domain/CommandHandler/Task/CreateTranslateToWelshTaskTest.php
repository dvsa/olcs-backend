<?php

/**
 * Create Translate To Welsh Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Task;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTranslateToWelshTask as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Task\CreateTranslateToWelshTask;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create Translate To Welsh Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateTranslateToWelshTaskTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateTranslateToWelshTask();

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['licence' => 111, 'description' => 'foo']);

        /** @var Team $team */
        $team = m::mock(Team::class)->makePartial();
        $team->setId(321);

        /** @var User $user */
        $user = m::mock(User::class)->makePartial();
        $user->setId(123);
        $user->setTeam($team);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($user);

        $result = new Result();

        $data = [
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK,
            'description' => 'Welsh translation required: foo',
            'urgent' => 'Y',
            'licence' => 111,
            'assignedToUser' => 123,
            'assignedToTeam' => 321,
            'actionDate' => null,
            'isClosed' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'irfoOrganisation' => null
        ];

        $this->expectedSideEffect(CreateTask::class, $data, $result);

        $this->assertSame($result, $this->sut->handleCommand($command));
    }
}
