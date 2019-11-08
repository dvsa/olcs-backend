<?php

namespace Dvsa\Olcs\Api\Service\Permits\Checkable;

use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepository;
use Dvsa\Olcs\Api\Entity\Task\Task;

class CheckedValueUpdater
{
    /** @var TaskRepository */
    private $taskRepo;

    /**
     * Create service instance
     *
     * @param TaskRepository $taskRepo
     *
     * @return CheckedValueUpdater
     */
    public function __construct(TaskRepository $taskRepo)
    {
        $this->taskRepo = $taskRepo;
    }

    /**
     * Update the checked value for the specified application if required, and close and save the associated task if one
     * exists
     *
     * @param CheckableApplicationInterface $application
     * @param bool|null $checked
     */
    public function updateIfRequired(CheckableApplicationInterface $application, $checked)
    {
        if ($application->requiresPreAllocationCheck()) {
            $application->updateChecked($checked);

            if ($checked) {
                $task = $application->fetchOpenSubmissionTask();

                if ($task instanceof Task) {
                    $task->setIsClosed('Y');
                    $this->taskRepo->save($task);
                }
            }
        }
    }
}
