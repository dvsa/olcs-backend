<?php

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee as CreateFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as Cmd;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateApplicationFee extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['FeeType'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $taskId = null;

        if ($this->shouldCreateTask()) {

            $taskResult = $this->handleSideEffect($this->createCreateTaskCommand($command));
            $result->merge($taskResult);

            $taskId = $taskResult->getId('task');
        }

        $result->merge($this->handleSideEffect($this->createCreateFeeCommand($command, $taskId)));

        return $result;
    }

    /**
     * @param Cmd $command
     * @return CreateTask
     */
    private function createCreateTaskCommand(Cmd $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $currentUser = $this->getCurrentUser();

        $data = [
            'category' => Task::CATEGORY_APPLICATION,
            'subCategory' => Task::SUBCATEGORY_FEE_DUE,
            'description' => 'Application Fee Due',
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => $currentUser->getId(),
            'assignedToTeam' => $currentUser->getTeam()->getId(),
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId()
        ];

        return CreateTask::create($data);
    }

    /**
     * @param Cmd $command
     * @param $taskId
     * @return CreateFeeCmd
     */
    private function createCreateFeeCommand(Cmd $command, $taskId)
    {
        $feeType = $command->getFeeTypeFeeType() == null ? FeeType::FEE_TYPE_APP : $command->getFeeTypeFeeType();

        $data = [
            'id' => $command->getId(),
            'feeTypeFeeType' => $feeType,
            'task' => $taskId
        ];

        return CreateFeeCmd::create($data);
    }

    /**
     * Check whether we need to create a task
     *
     * @return boolean
     */
    private function shouldCreateTask()
    {
        return $this->isGranted(Permission::INTERNAL_USER);
    }
}
