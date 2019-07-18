<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use DateTime;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;

class WindowAvailabilityChecker
{
    /** @var IrhpPermitWindowRepository */
    private $irhpPermitWindowRepo;

    /** @var StockAvailabilityChecker */
    private $stockAvailabilityChecker;

    /**
     * Create service instance
     *
     * @param IrhpPermitWindowRepository $irhpPermitWindowRepo
     * @param StockAvailabilityChecker $stockAvailabilityChecker
     *
     * @return WindowAvailabilityChecker
     */
    public function __construct(
        IrhpPermitWindowRepository $irhpPermitWindowRepo,
        StockAvailabilityChecker $stockAvailabilityChecker
    ) {
        $this->irhpPermitWindowRepo = $irhpPermitWindowRepo;
        $this->stockAvailabilityChecker = $stockAvailabilityChecker;
    }

    /**
     * Whether there are any short term ecmt windows both open and having available stock
     *
     * @param DateTime $now
     *
     * @return bool
     */
    public function hasAvailability(DateTime $now)
    {
        $openWindows = $this->irhpPermitWindowRepo->fetchOpenWindowsByType(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            $now
        );

        foreach ($openWindows as $openWindow) {
            $windowHasAvailability = $this->stockAvailabilityChecker->hasAvailability(
                $openWindow->getIrhpPermitStock()->getId()
            );

            if ($windowHasAvailability) {
                return true;
            }
        }

        return false;
    }
}
