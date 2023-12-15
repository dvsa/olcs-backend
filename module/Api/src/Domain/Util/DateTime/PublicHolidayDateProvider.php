<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

use Dvsa\Olcs\Api\Domain\Repository\PublicHoliday;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

final class PublicHolidayDateProvider implements DateProviderInterface
{
    private PublicHoliday $repository;
    private ?TrafficArea $trafficArea;

    public function __construct(PublicHoliday $repository, ?TrafficArea $trafficArea)
    {
        $this->repository = $repository;
        $this->trafficArea = $trafficArea;
    }

    public function between(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->repository->fetchBetweenByTa($startDate, $endDate, $this->trafficArea);
    }
}
