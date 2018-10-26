<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
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

    protected $extraRepos = ['IrhpPermitApplication', 'IrhpCandidatePermit', 'IrhpPermitWindow', 'IrhpPermitStock'];


    /**
     * Submit the ECMT application
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplication       $application
         * @var EcmtSubmitApplicationCmd    $command
         */
        $id = $command->getId();
        $newStatus = $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_UNDER_CONSIDERATION);

        $ecmtStock = $this->getRepo('IrhpPermitStock')->getNextIrhpPermitStockByPermitType(EcmtPermitApplication::PERMIT_TYPE, new DateTime());
        $ecmtStockId = $ecmtStock->getId();

        $ecmtWindow = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByStockId($ecmtStockId);
        $ecmtWindowId = $ecmtWindow->getId();

        $application = $this->getRepo()->fetchById($id);
        $application->submit($newStatus);

        $this->getRepo()->save($application);

        $irhpApplication = $this->createIrhpPermitApplication($application, $ecmtWindowId);
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
     * @param EcmtPermitApplication $ecmtPermitApplication
     * @param int $ecmtWindowId
     *
     * @return IrhpPermitApplicationEntity
     */
    private function createIrhpPermitApplication(EcmtPermitApplication $ecmtPermitApplication, int $ecmtWindowId)
    {
        return IrhpPermitApplicationEntity::createNew(
            $this->getRepo()->getReference(IrhpPermitWindowEntity::class, $ecmtWindowId),
            $ecmtPermitApplication->getLicence(),
            $ecmtPermitApplication
        );
    }

    /**
     * @param int $permitsRequired
     * @param IrhpPermitApplicationEntity $irhpPermitApplication
     */
    private function createIrhpCandidatePermitRecords(int $permitsRequired, IrhpPermitApplicationEntity $irhpPermitApplication)
    {
        $intensityOfUse = floatval($irhpPermitApplication->getPermitIntensityOfUse());
        $applicationScore = floatval($irhpPermitApplication->getPermitApplicationScore());

        for ($i = 0; $i < $permitsRequired; $i++) {
            $candidatePermit = IrhpCandidatePermitEntity::createNew(
                $irhpPermitApplication,
                $intensityOfUse,
                $applicationScore
            );
            $this->getRepo('IrhpCandidatePermit')->save($candidatePermit);
        }
    }
}
