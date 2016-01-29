<?php

/**
 * Update FinancialEvidence Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update FinancialEvidence Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateFinancialEvidenceStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'FinancialEvidence';

    protected function isSectionValid(Application $application)
    {
        return true;
    }
}
