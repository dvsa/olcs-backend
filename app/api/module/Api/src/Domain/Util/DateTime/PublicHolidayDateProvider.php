<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

use Dvsa\Olcs\Api\Domain\Repository\PublicHoliday;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

/**
 * Class PublicHolidayDateProvider
 */
final class PublicHolidayDateProvider implements DateProviderInterface
{
    /**
     * @var PublicHoliday
     */
    private $repository;
    /**
     * @var TrafficArea
     */
    private $trafficArea;

    /**
     * @param PublicHoliday $repository
     * @param TrafficArea $trafficArea
     */
    public function __construct(PublicHoliday $repository, TrafficArea $trafficArea)
    {
        $this->repository = $repository;
        $this->trafficArea = $trafficArea;
    }

    /**
     * Returns an array of dates between $startDate and $endDate implementation determines which dates apply
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public function between(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->repository->fetchBetweenByTa($startDate, $endDate, $this->trafficArea);
    }
}
