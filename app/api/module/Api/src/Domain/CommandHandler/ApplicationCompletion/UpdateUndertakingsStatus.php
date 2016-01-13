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
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Update Undertakings Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateUndertakingsStatus extends AbstractUpdateStatus implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    protected $section = 'Undertakings';

    protected function isSectionValid(Application $application)
    {
        if ($this->isInternalUser()) {
            return $application->getAuthSignature();
        }
        return $application->getDeclarationConfirmation() === 'Y';
    }
}
