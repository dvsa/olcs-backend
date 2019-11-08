<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
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
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        $irhpApplication->submit(
            $this->refData($irhpApplication->getSubmissionStatus())
        );

        $this->getRepo()->save($irhpApplication);

        $sideEffects = [];

        if ($irhpApplication->shouldAllocatePermitsOnSubmission()) {
            $sideEffects[] = $this->createQueue(
                $irhpApplicationId,
                Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE,
                []
            );
        }

        $sideEffects[] = $this->getCreateTaskCommand($irhpApplication);

        $sideEffects[] = $this->createQueue(
            $irhpApplicationId,
            Queue::TYPE_PERMITS_POST_SUBMIT,
            ['irhpPermitType' => $irhpApplication->getIrhpPermitType()->getId()]
        );

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
     *
     * @return CreateTask
     */
    private function getCreateTaskCommand(IrhpApplication $irhpApplication)
    {
        return CreateTask::create(
            [
                'category' => Task::CATEGORY_PERMITS,
                'subCategory' => Task::SUBCATEGORY_APPLICATION,
                'description' => $irhpApplication->getSubmissionTaskDescription(),
                'irhpApplication' => $irhpApplication->getId(),
                'licence' => $irhpApplication->getLicence()->getId()
            ]
        );
    }
}
