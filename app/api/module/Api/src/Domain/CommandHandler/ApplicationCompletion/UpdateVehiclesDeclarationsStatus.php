<?php

/**
 * Update VehiclesDeclarations Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update VehiclesDeclarations Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateVehiclesDeclarationsStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'VehiclesDeclarations';

    protected function isSectionValid(Application $application)
    {
        $yn = ['Y', 'N'];

        $total = (int)$application->getTotAuthSmallVehicles()
            + (int)$application->getTotAuthMediumVehicles()
            + (int)$application->getTotAuthLargeVehicles();

        if ($total < 1) {
            return false;
        }

        if (!in_array($application->getPsvLimousines(), $yn)) {
            return false;
        }
        if (!in_array($application->getPsvNoLimousineConfirmation(), $yn)) {
            return false;
        }

        if (empty($application->getTotAuthSmallVehicles())) {

            if (!in_array($application->getPsvNoSmallVhlConfirmation(), $yn)) {
                return false;
            }

            if (!in_array($application->getPsvOnlyLimousinesConfirmation(), $yn)) {
                return false;
            }

            return true;
        }

        if (!in_array($application->getPsvSmallVhlConfirmation(), $yn)) {
            return false;
        }

        if ($application->getPsvOperateSmallVhl() === 'Y'
            && empty($application->getPsvSmallVhlNotes())) {
            return false;
        }

        if ((!empty($application->getTotAuthMediumVehicles()) || !empty($application->getTotAuthLargeVehicles()))
            && !in_array($application->getPsvOnlyLimousinesConfirmation(), $yn)) {
            return false;
        }

        $isScotland = false;

        if ($application->getLicence()->getTrafficArea() !== null
            && $application->getLicence()->getTrafficArea()->getIsScotland()) {
            $isScotland = $application->getLicence()->getTrafficArea()->getIsScotland();
        }

        if (!$isScotland) {

            if (!in_array($application->getPsvOperateSmallVhl(), $yn)) {
                return false;
            }

            if (empty($application->getPsvSmallVhlNotes())) {
                return false;
            }
        }

        return true;
    }
}
