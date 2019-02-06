<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpPermitApplicationPermit as AllocateIrhpPermitApplicationPermitCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use DateTime;
use Olcs\Logging\Log\Logger;
use RuntimeException;

/**
 * Allocate a single permit for an IRHP Permit application
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class AllocateIrhpPermitApplicationPermit extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitApplication';

    protected $extraRepos = ['IrhpPermit'];

    /**
     * Handle command
     *
     * @param AllocateIrhpPermitApplicationPermitCmd|CommandInterface $command command
     *
     * @return Result
     *
     * @throws RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpPermitApplication = $this->getRepo()->fetchById(
            $command->getId()
        );

        $irhpPermitRanges = $irhpPermitApplication
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getNonReservedNonReplacementRangesOrderedByFromNo();

        foreach ($irhpPermitRanges as $irhpPermitRange) {
            $assignedPermitNumbers = $this->getRepo('IrhpPermit')->getAssignedPermitNumbersByRange(
                $irhpPermitRange->getId()
            );

            if (count($assignedPermitNumbers) < $irhpPermitRange->getSize()) {
                $this->addIrhpPermit($irhpPermitApplication, $irhpPermitRange, $assignedPermitNumbers);
                return $this->result;
            }
        }

        $message = sprintf(
            'Unable to find range with free permits for irhp permit application %d',
            $irhpPermitApplication->getId()
        );

        Logger::warn($message);
        throw new RuntimeException($message);
    }

    /**
     * Derive an IrhpPermit entity and save it to the repository
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param IrhpPermitRange $irhpPermitRange
     * @param array $assignedPermitNumbers
     */
    private function addIrhpPermit(
        IrhpPermitApplication $irhpPermitApplication,
        IrhpPermitRange $irhpPermitRange,
        array $assignedPermitNumbers
    ) {
        $permitNumber = $this->getNextPermitNumber($irhpPermitRange, $assignedPermitNumbers);

        $irhpPermit = IrhpPermit::createForIrhpApplication(
            $irhpPermitApplication,
            $irhpPermitRange,
            new DateTime(),
            $this->refData(IrhpPermit::STATUS_PENDING),
            $permitNumber
        );

        $this->getRepo('IrhpPermit')->save($irhpPermit);

        $this->result->addMessage(
            sprintf(
                'Allocated permit number %d in range %d for irhp permit application %d',
                $permitNumber,
                $irhpPermitRange->getId(),
                $irhpPermitApplication->getId()
            )
        );
    }

    /**
     * Get the first available permit number from the specified range
     *
     * @param IrhpPermitRange $irhpPermitRange
     * @param array $assignedPermitNumbers
     *
     * @return int
     */
    private function getNextPermitNumber(IrhpPermitRange $irhpPermitRange, array $assignedPermitNumbers)
    {
        $permitMap = array_fill_keys(
            range($irhpPermitRange->getFromNo(), $irhpPermitRange->getToNo()),
            true
        );

        foreach ($assignedPermitNumbers as $permitNumber) {
            $permitMap[$permitNumber] = false;
        }

        $assignedPermitMap = array_filter($permitMap);
        reset($assignedPermitMap);

        return key($assignedPermitMap);
    }
}
