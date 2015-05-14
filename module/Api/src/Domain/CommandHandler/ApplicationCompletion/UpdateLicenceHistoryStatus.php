<?php

/**
 * Update LicenceHistory Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;

/**
 * Update LicenceHistory Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateLicenceHistoryStatus extends AbstractUpdateStatus
{
    protected $repoServiceName = 'Application';

    protected $section = 'LicenceHistory';

    protected function isSectionValid(Application $application)
    {
        $licenceTypes = [
            'prev_has_licence' => $application->getPrevHasLicence(),
            'prev_had_licence' => $application->getPrevHasLicence(),
            'prev_been_refused' => $application->getPrevBeenRefused(),
            'prev_been_revoked' => $application->getPrevBeenRevoked(),
            'prev_been_disqualified' => $application->getPrevBeenDisqualifiedTc(),
            'prev_been_at_pi' => $application->getPrevBeenAtPi(),
            'prev_purchased_assets' => $application->getPrevPurchasedAssets()
        ];

        $yn = ['Y', 'N'];
        $hasAnsweredYes = false;
        $yesLicenceTypes = [];

        // Separate foreachs is more efficient, if any of the answers are not Y/N then we won't bother fetching the
        // other licences
        foreach ($licenceTypes as $licenceType => $answer) {

            if (!in_array($answer, $yn)) {
                return false;
            }

            if ($answer === 'Y') {
                $hasAnsweredYes = true;
                $yesLicenceTypes[] = $licenceType;
            }
        }

        // Can return early if there are no yes answers
        if (!$hasAnsweredYes) {
            return true;
        }

        $otherLicences = $application->getOtherLicences();

        foreach ($yesLicenceTypes as $licenceType) {

            $hasLicences = false;

            /** @var OtherLicence $otherLicence */
            foreach ($otherLicences as $otherLicence) {

                if ($otherLicence->getPreviousLicenceType()->getId() === $licenceType) {
                    $hasLicences = true;
                    continue 2;
                }
            }

            if (!$hasLicences) {
                return false;
            }
        }

        return true;
    }
}
