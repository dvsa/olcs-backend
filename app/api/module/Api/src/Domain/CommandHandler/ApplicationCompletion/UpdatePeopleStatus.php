<?php

/**
 * Update People Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update People Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdatePeopleStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'People';

    protected function isSectionValid(Application $application)
    {
        $num = count($application->getLicence()->getOrganisation()->getOrganisationPersons())
            + count($application->getApplicationOrganisationPersons());
        return ($num > 0);
    }
}
