<?php

/**
 * Update Safety Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Safety Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateSafetyStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'Safety';

    protected function isSectionValid(Application $application)
    {
        $licence = $application->getLicence();

        if ((int)$licence->getSafetyInsVehicles() < 1) {
            return false;
        }

        if ((int)$licence->getSafetyInsVaries() < 1) {
            return false;
        }

        if ($licence->getTachographIns() === null) {
            return false;
        }

        if (count($licence->getWorkshops()) < 1) {
            return false;
        }

        if (!in_array($application->getSafetyConfirmation(), ['Y', 'N'])) {
            return false;
        }

        if ($licence->getTachographIns()->getId() === Licence::TACH_EXT && empty($licence->getTachographInsName())) {
            return false;
        }

        if ($application->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            && empty($licence->getSafetyInsTrailers())) {
            return false;
        }

        return true;
    }
}
