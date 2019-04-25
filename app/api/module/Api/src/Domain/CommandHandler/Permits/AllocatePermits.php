<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtIssued;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocatePermits as AllocatePermitsCmd;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use DateTime;

/**
 * Allocate permits for an ECMT Permit application
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class AllocatePermits extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermit'];

    /**
     * Handle command
     *
     * @param AllocatePermitsCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermitApplicationId = $command->getId();

        $ecmtPermitApplication = $this->getRepo()->fetchById($ecmtPermitApplicationId);
        $this->getRepo()->refresh($ecmtPermitApplication);

        $irhpPermitApplication = $ecmtPermitApplication->getFirstIrhpPermitApplication();
        $candidatePermits = $irhpPermitApplication->getSuccessfulIrhpCandidatePermits();
        foreach ($candidatePermits as $candidatePermit) {
            $this->addIrhpPermit($candidatePermit);
        }

        $ecmtPermitApplication->proceedToValid(
            $this->refData(EcmtPermitApplication::STATUS_VALID)
        );
        $this->getRepo()->save($ecmtPermitApplication);

        $this->result->merge(
            $this->handleSideEffect(
                $this->emailQueue(
                    SendEcmtIssued::class,
                    [ 'id' => $ecmtPermitApplicationId ],
                    $ecmtPermitApplicationId
                )
            )
        );

        $this->result->addId('ecmtPermitApplication', $ecmtPermitApplicationId);
        $this->result->addMessage('Permit allocation complete for ECMT application');

        return $this->result;
    }

    /**
     * Derive an IrhpPermit entity from the IrhpCandidatePermit entity and save it to the repository
     *
     * @param IrhpCandidatePermit $candidatePermit
     */
    private function addIrhpPermit(IrhpCandidatePermit $candidatePermit)
    {
        $range = $candidatePermit->getIrhpPermitRange();

        $irhpPermit = IrhpPermit::createNew(
            $candidatePermit,
            new DateTime(),
            $this->refData(IrhpPermit::STATUS_PENDING),
            $this->getNextPermitNumber($range)
        );

        $this->getRepo('IrhpPermit')->save($irhpPermit);
        $range->addIrhpPermits($irhpPermit);
    }

    /**
     * Get the first available permit number from the specified range
     *
     * @param IrhpPermitRange $range
     *
     * @return int
     */
    private function getNextPermitNumber(IrhpPermitRange $range)
    {
        $permitMap = array_fill_keys(
            range($range->getFromNo(), $range->getToNo()),
            true
        );

        foreach ($range->getIrhpPermits() as $permit) {
            $permitMap[$permit->getPermitNumber()] = false;
        }

        $assignedPermitMap = array_filter($permitMap);
        reset($assignedPermitMap);
        return key($assignedPermitMap);
    }
}
