<?php

/**
 * Update ConvictionsPenalties Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update ConvictionsPenalties Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateConvictionsPenaltiesStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'ConvictionsPenalties';

    protected function isSectionValid(Application $application)
    {
        if (!in_array($application->getPrevConviction(), ['Y', 'N'])) {
            return false;
        }

        if ($application->getConvictionsConfirmation() !== 'Y') {
            return false;
        }

        if ($application->getPrevConviction() === 'Y' && count($application->getPreviousConvictions()) < 1) {
            return false;
        }

        return true;
    }
}
