<?php

/**
 * Update Undertakings Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Undertakings Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateUndertakingsStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'Undertakings';

    protected function isSectionValid(Application $application)
    {
        return $application->getDeclarationConfirmation() === 'Y';
    }
}
