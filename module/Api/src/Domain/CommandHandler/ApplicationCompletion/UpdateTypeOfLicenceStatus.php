<?php

/**
 * Update Type Of Licence Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Type Of Licence Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTypeOfLicenceStatus extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $completion = $application->getApplicationCompletion();

        $currentStatus = $completion->getTypeOfLicenceStatus();

        if ($this->isSectionValid($application)) {
            $newStatus = ApplicationCompletion::STATUS_COMPLETE;
        } else {
            $newStatus = ApplicationCompletion::STATUS_INCOMPLETE;
        }

        $result = new Result();

        // Statuses are the same so we can bail
        if ($newStatus === $currentStatus) {
            $result->addMessage('Type of licence section status is unchanged');
        } else {
            $result->addMessage('Type of licence section status has been updated');
            $completion->setTypeOfLicenceStatus($newStatus);
            $this->getRepo()->save($application);
        }

        return $result;
    }

    private function isSectionValid(Application $application)
    {
        if (!in_array($application->getNiFlag(), ['Y', 'N'])) {
            return false;
        }

        if ($application->getGoodsOrPsv() === null) {
            return false;
        }

        if ($application->getLicenceType() === null) {
            return false;
        }

        return $application->isValidTol(
            $application->getNiFlag(),
            $application->getGoodsOrPsv(),
            $application->getLicenceType()
        );
    }
}
