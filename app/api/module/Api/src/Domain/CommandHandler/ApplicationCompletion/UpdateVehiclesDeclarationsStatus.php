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
use Dvsa\Olcs\Api\Entity\Licence\Licence;

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
        $isScotland = false;
        if ($application->getLicence()->getTrafficArea() !== null) {
            $isScotland = $application->getLicence()->getTrafficArea()->getIsScotland() == true;
        }
        try {
            $this->validate15bi($application, $isScotland);
            $this->validate15bii($application, $isScotland);
            $this->validate15cd($application, $isScotland);
            $this->validate15e($application);
            $this->validate15fi($application);
            $this->validate15fii($application);
            $this->validate15g($application);
            $this->validate8bi($application);
            $this->validate8bii($application);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    // 15b[i]
    protected function validate15bi($application, $isScotland)
    {
        if ((int) $application->getTotAuthSmallVehicles() > 0 && !$isScotland) {
            if (!in_array($application->getPsvOperateSmallVhl(), ['Y', 'N'])) {
                throw new \Exception('15bi');
            }
        }
    }

    // 15b[ii]
    protected function validate15bii($application, $isScotland)
    {
        if ((int) $application->getTotAuthSmallVehicles() > 0 && !$isScotland &&
            $application->getPsvOperateSmallVhl() === 'Y'
            ) {
            if (empty($application->getPsvSmallVhlNotes())) {
                throw new \Exception('15bii');
            }
        }
    }

    // 15c/d
    protected function validate15cd($application, $isScotland)
    {
        if ((int) $application->getTotAuthSmallVehicles() > 0 && !$isScotland &&
            $application->getPsvOperateSmallVhl() === 'N'
            ) {
            if ($application->getPsvSmallVhlConfirmation() !== 'Y') {
                throw new \Exception('15c');
            }
        }
        if ((int) $application->getTotAuthSmallVehicles() > 0 && $isScotland) {
            if ($application->getPsvSmallVhlConfirmation() !== 'Y') {
                throw new \Exception('15d');
            }
        }
    }

    // 15e
    protected function validate15e($application)
    {
        if ((int) $application->getTotAuthSmallVehicles() === 0 &&
            ((int) $application->getTotAuthMediumVehicles() > 0 || (int) $application->getTotAuthLargeVehicles() > 0)
            ) {
            if ($application->getPsvNoSmallVhlConfirmation() !== 'Y') {
                throw new \Exception('15e');
            }
        }
    }

    // 8b[i]
    protected function validate8bi($application)
    {
        if ($application->getLicenceType()->getId() === Licence::LICENCE_TYPE_RESTRICTED &&
            (int) $application->getTotAuthMediumVehicles() > 0
            ) {
            if ($application->getPsvMediumVhlConfirmation() !== 'Y') {
                throw new \Exception('8bi');
            }
        }
    }

    // 8b[ii]
    protected function validate8bii($application)
    {
        if ($application->getLicenceType()->getId() === Licence::LICENCE_TYPE_RESTRICTED &&
            (int) $application->getTotAuthMediumVehicles() > 0
            ) {
            if (empty($application->getPsvMediumVhlNotes())) {
                throw new \Exception('8bii');
            }
        }
    }

    // 15f[i]
    protected function validate15fi($application)
    {
        if (!in_array($application->getPsvLimousines(), ['Y', 'N'])) {
            throw new \Exception('15fi');
        }
    }

    // 15f[ii]
    protected function validate15fii($application)
    {
        if ($application->getPsvLimousines() === 'N') {
            if ($application->getPsvNoLimousineConfirmation() !== 'Y') {
                throw new \Exception('15fii');
            }
        }
    }

    // 15g
    protected function validate15g($application)
    {
        if ($application->getPsvLimousines() === 'Y' &&
            ((int) $application->getTotAuthMediumVehicles() > 0 || (int) $application->getTotAuthLargeVehicles() > 0)
            ) {
            if ($application->getPsvOnlyLimousinesConfirmation() !== 'Y') {
                throw new \Exception('15g');
            }
        }
    }
}
