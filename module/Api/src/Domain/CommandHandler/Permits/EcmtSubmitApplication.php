<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

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
use Dvsa\Olcs\Transfer\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;
use Zend\View\Model\ViewModel;

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

        $application = $this->getRepo()->fetchById($id);
        $application->submit($newStatus);

        $this->getRepo()->save($application);

        $this->createIrhpCandidatePermitRecords(
            $application->getPermitsRequired(),
            $application->getFirstIrhpPermitApplication()
        );

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('Permit application updated');

        $emailCmd = $this->emailQueue(SendEcmtAppSubmittedCmd::class, ['id' => $id], $id);

        $data = $this->createSnapshotData($application);
        $view = new ViewModel();
        $view->setTemplate('sections/application-snapshot');
        //$view->setTemplate('sections/applicants-responses');

        $view->setVariable('data', $data);

        $html = $this->getCommandHandler()->getServiceLocator()->get('ViewRenderer')->render($view);
        $snapshotCmd = SnapshotCmd::create(['id' => $id, 'html' => $html]);

        //queue the email confirming submission
        $result->merge(
            $this->handleSideEffects([$emailCmd, $snapshotCmd])
        );

        return $result;
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

    /**
     * @param object $application
     */
    private function createSnapshotData($application)
    {
        $data['permitType'] = $application->getPermitType()->getDescription();
        $data['operator'] = $application->getLicence()->getOrganisation()->getName();
        $data['ref'] = $application->getApplicationRef();
        $data['licence'] = $application->getLicence()->getLicNo();
        $data['emissions'] =  ($application->getEmissions() === 1) ? 'Yes' : 'No';
        $data['cabotage'] = ($application->getCabotage() === 1) ? 'Yes' : 'No';
        $data['limited-permits'] = ($application->getHasRestrictedCountries() === 1) ? 'Yes' : 'No';
        $data['number-required'] = $application->getPermitsRequired();
        $data['trips'] = $application->getTrips();
        $data['int-journeys'] = $application->getInternationalJourneys()->getDescription();
        $data['goods'] = $application->getSectors()->getName();

        return $data;
    }
}
