<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\StoreSnapshot as SnapshotCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplication as SubmitApplicationCmd;

/**
 * Command Handler to action the submission of an IrhpApplication
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class SubmitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    /**
     * Handle command
     *
     * @param SubmitApplicationCmd|CommandInterface $command command
     *
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        switch ($irhpApplication->getIrhpPermitType()->getId()) {
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM:
                $irhpApplication->submit($this->refData(IrhpInterface::STATUS_UNDER_CONSIDERATION));
                $sideEffects = [
                    $this->getCreateTaskCommand($irhpApplication),
                    SnapshotCmd::create(['id' => $irhpApplicationId])
                ];
                break;
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL:
                $irhpApplication->submit($this->refData(IrhpInterface::STATUS_ISSUING));
                $sideEffects = [
                    SnapshotCmd::create(['id' => $irhpApplicationId]),
                    $this->createQueue($irhpApplicationId, Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE, [])
                ];
                break;
            default:
                throw new ValidationException(['Unsupported permit type, cannot proceed with submission.']);
        }

        $this->getRepo()->save($irhpApplication);

        $this->result->merge(
            $this->handleSideEffects($sideEffects)
        );

        $this->result->addMessage('IRHP application submitted');
        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * Get task creation command for an application
     *
     * @param IrhpApplication $irhpApplication
     * @return CreateTask
     */
    private function getCreateTaskCommand(IrhpApplication $irhpApplication)
    {
        return CreateTask::create(
            [
                'category' => Task::CATEGORY_PERMITS,
                'subCategory' => Task::SUBCATEGORY_APPLICATION,
                'description' => Task::TASK_DESCRIPTION_SHORT_TERM_ECMT_RECEIVED,
                'irhpApplication' => $irhpApplication->getId(),
                'licence' => $irhpApplication->getLicence()->getId()
            ]
        );
    }
}
