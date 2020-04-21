<?php

namespace Dvsa\Olcs\Api\Service\Permits\Allocate;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Service\Permits\Allocate\RangeMatchingCriteriaInterface;
use RuntimeException;

class IrhpPermitAllocator
{
    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return IrhpPermitAllocator
     */
    public function __construct(IrhpPermitRepository $irhpPermitRepo)
    {
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Allocate a permit number in accordance with the provided data and populate the provided result with a status
     * message
     *
     * @param Result $result
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param RangeMatchingCriteriaInterface $criteria (optional)
     * @param DateTime $expiryDate (optional)
     *
     * @throws RuntimeException
     */
    public function allocate(
        Result $result,
        IrhpPermitApplication $irhpPermitApplication,
        ?RangeMatchingCriteriaInterface $criteria,
        ?DateTime $expiryDate
    ) {
        $irhpPermitRanges = $irhpPermitApplication
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getNonReservedNonReplacementRangesOrderedByFromNo($criteria);

        foreach ($irhpPermitRanges as $irhpPermitRange) {
            $assignedPermitNumbers = $this->irhpPermitRepo->getAssignedPermitNumbersByRange(
                $irhpPermitRange->getId()
            );

            if (count($assignedPermitNumbers) < $irhpPermitRange->getSize()) {
                return $this->addIrhpPermit(
                    $result,
                    $irhpPermitApplication,
                    $irhpPermitRange,
                    $assignedPermitNumbers,
                    $expiryDate
                );
            }
        }

        $message = sprintf(
            'Unable to find range with free permits for irhp permit application %d',
            $irhpPermitApplication->getId()
        );

        throw new RuntimeException($message);
    }

    /**
     * Derive an IrhpPermit entity and save it to the repository, adding a status message to the provided result
     *
     * @param Result $result
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param IrhpPermitRange $irhpPermitRange
     * @param array $assignedPermitNumbers
     * @param DateTime|null $expiryDate
     */
    private function addIrhpPermit(
        Result $result,
        IrhpPermitApplication $irhpPermitApplication,
        IrhpPermitRange $irhpPermitRange,
        array $assignedPermitNumbers,
        ?DateTime $expiryDate
    ) {
        $permitNumber = $this->getNextPermitNumber($irhpPermitRange, $assignedPermitNumbers);

        $irhpPermit = IrhpPermit::createForIrhpApplication(
            $irhpPermitApplication,
            $irhpPermitRange,
            $irhpPermitApplication->generateIssueDate(),
            $this->irhpPermitRepo->getRefdataReference(IrhpPermit::STATUS_PENDING),
            $permitNumber,
            $expiryDate
        );

        $this->irhpPermitRepo->save($irhpPermit);

        $message = sprintf(
            'Allocated permit number %d in range %d for irhp permit application %d',
            $permitNumber,
            $irhpPermitRange->getId(),
            $irhpPermitApplication->getId()
        );

        $result->addMessage($message);
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
