<?php

/**
 * Update Conditions Undertakings Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Conditions Undertakings Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateConditionsUndertakingsStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'Conditions';

    protected function isSectionValid(Application $application)
    {
        return true;
    }
}
