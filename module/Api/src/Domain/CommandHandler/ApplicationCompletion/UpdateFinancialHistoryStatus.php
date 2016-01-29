<?php

/**
 * Update FinancialHistory Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update FinancialHistory Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateFinancialHistoryStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'FinancialHistory';

    protected function isSectionValid(Application $application)
    {
        if ($application->getInsolvencyConfirmation() !== 'Y') {
            return false;
        }

        $yesNos = [
            $application->getBankrupt(),
            $application->getLiquidation(),
            $application->getReceivership(),
            $application->getAdministration(),
            $application->getDisqualified()
        ];

        foreach ($yesNos as $yesNo) {
            if (!in_array($yesNo, ['Y', 'N'])) {
                return false;
            }

            if ($yesNo === 'Y' && strlen($application->getInsolvencyDetails()) < 200) {
                return false;
            }
        }

        return true;
    }
}
