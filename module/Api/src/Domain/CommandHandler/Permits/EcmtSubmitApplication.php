<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\EcmtSubmitApplication as EcmtSubmitApplicationCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as IrhpPermitRangeEntity;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;




/**
 * Submit the ECMT application
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class EcmtSubmitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitApplication', 'IrhpCandidatePermit'];


    /**
     * Submit the ECMT application
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws ForbiddenException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplication       $application
         * @var EcmtSubmitApplicationCmd    $command
         */
        $id = $command->getId();
        $newStatus = $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_UNDER_CONSIDERATION);
        $application = $this->getRepo()->fetchById($id);
        $application->submit($newStatus);

        $this->getRepo()->save($application);

        $irhpApplication = $this->createIrhpPermitApplication($application);
        $this->getRepo('IrhpPermitApplication')->save($irhpApplication);

        $this->createIrhpCandidatePermitRecords($application->getPermitsRequired(), $irhpApplication);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('Permit application updated');

        $emailCmd = $this->emailQueue(SendEcmtAppSubmittedCmd::class, ['id' => $id], $id);

        //queue the email confirming submission
        $result->merge(
            $this->handleSideEffect($emailCmd)
        );

        return $result;
    }

    /**
     * Creates a new Irhp_Permit_Application record
     * for the ecmt_permit_application being submitted.
     *
     * @todo: hardcoded the Id for the permitWindow and jurisdiction. Need to make this dynamic.
     */
    private function createIrhpPermitApplication(EcmtPermitApplication $ecmtPermitApplication)
    {
        return IrhpPermitApplicationEntity::createNew(
            $this->getRepo()->getReference(IrhpPermitWindowEntity::class, 1),
            $ecmtPermitApplication->getLicence(),
            $ecmtPermitApplication
        );
    }

    private function createIrhpCandidatePermitRecords(int $permitsRequired, IrhpPermitApplicationEntity $irhpPermitApplication)
    {
        $intensityOfUse = floatval($irhpPermitApplication->getPermitIntensityOfUse());
        $applicationScore = floatval($irhpPermitApplication->getPermitApplicationScore());
        $randomizedScore = null;

        for ($i = 0; $i < $permitsRequired; $i++) {

            $candidatePermit = IrhpCandidatePermitEntity::createNew(
                $irhpPermitApplication,
                $this->getRepo()->getReference(IrhpPermitRangeEntity::class, 2),
                $intensityOfUse,
                $randomizedScore,
                $applicationScore
            );

            $this->getRepo('IrhpCandidatePermit')->save($candidatePermit);
        }
    }
}
