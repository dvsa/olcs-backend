<?php

/**
 * Update Addresses Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Addresses Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateAddressesStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'Addresses';

    protected function isSectionValid(Application $application)
    {
        $licence = $application->getLicence();

        // Must have correspondence address
        $corAdd = $licence->getCorrespondenceCd();
        if ($corAdd === null) {
            return false;
        }

        // Must have at least 1 phone contact
        $phoneContacts = $corAdd->getPhoneContacts();
        if (count($phoneContacts) < 1) {
            return false;
        }

        $allowedLicTypes = array(
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        // If we don't need an establishment address
        if (in_array($application->getLicenceType()->getId(), $allowedLicTypes)) {
            $estAdd = $licence->getEstablishmentCd();

            if ($estAdd === null) {
                return false;
            }
        }

        return true;
    }
}
