<?php

/**
 * Update VehiclesPsv Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;

/**
 * Update VehiclesPsv Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateVehiclesPsvStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'VehiclesPsv';

    protected function isSectionValid(Application $application)
    {
        if ($application->getHasEnteredReg() === 'N') {
            return true;
        }

        if (empty($application->getLicence()->getLicenceVehicles())) {
            return false;
        }

        if ($application->getTotAuthSmallVehicles() === null) {
            return false;
        }

        if ($application->getTotAuthMediumVehicles() === null) {
            return false;
        }

        if ($application->getLicenceType()->getId() !== Licence::LICENCE_TYPE_RESTRICTED
            && $application->getTotAuthLargeVehicles() === null) {
            return false;
        }

        $psvTypes = [
            'small'  => Vehicle::PSV_TYPE_SMALL,
            'medium' => Vehicle::PSV_TYPE_MEDIUM
        ];

        if ($application->getLicenceType()->getId() !== Licence::LICENCE_TYPE_RESTRICTED) {
            $psvTypes['large'] = Vehicle::PSV_TYPE_LARGE;
        }

        $small = $medium = $large = 0;

        /** @var LicenceVehicle $licenceVehicle */
        foreach ($application->getLicence()->getLicenceVehicles() as $licenceVehicle) {
            if ($licenceVehicle->getVehicle()->getPsvType() !== null) {
                $psvType = array_search($licenceVehicle->getVehicle()->getPsvType()->getId(), $psvTypes);
                ${$psvType}++;
            }
        }

        if ($small > $application->getTotAuthSmallVehicles()) {
            return false;
        }

        if ($medium > $application->getTotAuthMediumVehicles()) {
            return false;
        }

        if ($application->getLicenceType()->getId() !== Licence::LICENCE_TYPE_RESTRICTED
            && $large > $application->getTotAuthLargeVehicles()) {
            return false;
        }

        return true;
    }
}
