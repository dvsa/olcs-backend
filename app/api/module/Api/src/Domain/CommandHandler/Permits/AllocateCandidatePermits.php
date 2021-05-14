<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateCandidatePermits as AllocateCandidatePermitsCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use DateTime;

/**
 * Allocate candidate permits for an IRHP Permit application
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AllocateCandidatePermits extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrhpPermitApplication';

    protected $extraRepos = ['IrhpPermit'];

    /**
     * Handle command
     *
     * @param AllocateCandidatePermitsCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var IrhpPermitApplication $irhpPermitApplication
         * @var IrhpPermitType        $irhpPermitType
         */
        $irhpPermitApplicationId = $command->getId();
        $irhpPermitApplication = $this->getRepo()->fetchById($irhpPermitApplicationId);
        $irhpPermitType = $irhpPermitApplication->getIrhpApplication()->getIrhpPermitType();

        $expiryDate = null;

        //for short term permits only, we add an expiry date on the permit
        if ($irhpPermitType->isEcmtShortTerm()) {
            $expiryDate = $irhpPermitApplication->generateExpiryDate();
        }

        $candidatePermits = $irhpPermitApplication->getSuccessfulIrhpCandidatePermits(null, true);
        foreach ($candidatePermits as $candidatePermit) {
            $this->addIrhpPermit($candidatePermit, $expiryDate);
        }

        $this->result->addId('irhpPermitApplication', $irhpPermitApplicationId);
        $this->result->addMessage('IRHP permit records created');

        return $this->result;
    }

    /**
     * Derive an IrhpPermit entity from the IrhpCandidatePermit entity and save it to the repository
     *
     * @param IrhpCandidatePermit $candidatePermit
     * @param DateTime|null       $expiryDate
     *
     * @return void
     */
    private function addIrhpPermit(IrhpCandidatePermit $candidatePermit, ?DateTime $expiryDate): void
    {
        $range = $candidatePermit->getIrhpPermitRange();

        $irhpPermit = IrhpPermit::createNew(
            $candidatePermit,
            new DateTime(),
            $this->refData(IrhpPermit::STATUS_PENDING),
            $this->getNextPermitNumber($range),
            $expiryDate
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
