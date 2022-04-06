<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Application reset trait
 */
trait ApplicationResetTrait
{
    /**
     * Normalise the received date into a format suitable to be passed to the create application/variation command
     *
     * @param mixed $receivedDate
     *
     * @return string|null
     */
    private function processReceivedDate($receivedDate)
    {
        if ($receivedDate !== null) {
            if ($receivedDate instanceof DateTime) {
                $receivedDate = $receivedDate->format('Y-m-d');
            }

            return $receivedDate;
        }

        return null;
    }

    /**
     * Close all open tasks relating to the application/variation
     *
     * @param Application $application
     *
     * return int
     */
    private function closeTasks(Application $application): int
    {
        $count = 0;

        foreach ($application->getTasks() as $task) {
            if ($task->getIsClosed() === 'N') {
                $count++;
                $task->setIsClosed('Y');
            }
        }

        $this->getRepo()->save($application);

        return $count;
    }
}
