<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use RuntimeException;

class ApplicationPathAnswersUpdaterProvider
{
    private $updaters = [];

    /**
     * Get the updater instance corresponding to the specified application path group id
     *
     * @param int $applicationPathGroupId

     * @return ApplicationPathAnswersUpdaterInterface
     *
     * @throws RuntimeException
     */
    public function getByApplicationPathGroupId($applicationPathGroupId)
    {
        if (!isset($this->updaters[$applicationPathGroupId])) {
            $message = sprintf(
                'Unable to find updater corresponding to application path group id %s',
                $applicationPathGroupId
            );

            throw new RuntimeException($message);
        }

        return $this->updaters[$applicationPathGroupId];
    }

    /**
     * Register an updater instance corresponding to an application path group id
     *
     * @param int $applicationPathGroupId
     * @param ApplicationPathAnswersUpdaterInterface $updater
     */
    public function registerUpdater($applicationPathGroupId, ApplicationPathAnswersUpdaterInterface $updater)
    {
        $this->updaters[$applicationPathGroupId] = $updater;
    }
}
