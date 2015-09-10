<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * CloseTexTask
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CloseTexTask extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $application Application */
        $application = $this->getRepo()->fetchUsingId($command);

        $taskIdsToClose = [];

        /* @var $task Task */
        // If a 'TEX' task already exists that is linked to the current application, set the status to 'Closed' and
        foreach ($application->getTasks() as $task) {
            if ($task->getCategory()->getId() === Category::CATEGORY_APPLICATION &&
                $task->getSubCategory()->getId() === Category::TASK_SUB_CATEGORY_TIME_EXPIRED
            ) {
                $taskIdsToClose[] = $task->getId();
            }
        };

        return $this->handleSideEffect(
            \Dvsa\Olcs\Transfer\Command\Task\CloseTasks::create(['ids' => $taskIdsToClose])
        );
    }
}
