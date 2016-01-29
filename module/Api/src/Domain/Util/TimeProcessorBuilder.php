<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\System\Sla;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Domain\Util\DateTime\AddDays;
use Dvsa\Olcs\Api\Domain\Util\DateTime\AddDaysExcludingDates;
use Dvsa\Olcs\Api\Domain\Util\DateTime\AddWorkingDays;
use Dvsa\Olcs\Api\Domain\Util\DateTime\PublicHolidayDateProvider;

/**
 * Class TimeProcessorBuilder
 * @package Dvsa\Olcs\Api\Domain\Util
 */
class TimeProcessorBuilder implements TimeProcessorBuilderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $publicHolidayRepo;

    /**
     * @param RepositoryInterface $publicHolidayRepo
     */
    public function __construct(RepositoryInterface $publicHolidayRepo)
    {
        $this->publicHolidayRepo = $publicHolidayRepo;
    }

    /**
     * Builds a time processor for calculating sla dates based upon an Sla and a TrafficArea
     *
     * @param Sla $sla
     * @param TrafficArea $trafficArea
     * @return AddDays|AddDaysExcludingDates|AddWorkingDays
     */
    public function build(Sla $sla, TrafficArea $trafficArea)
    {
        $dateTimeProcessor = new AddDays();
        if ($sla->getWeekend()) {
            $dateTimeProcessor = new AddWorkingDays($dateTimeProcessor);
        }

        if ($sla->getPublicHoliday()) {
            $dateProvider = new PublicHolidayDateProvider($this->publicHolidayRepo, $trafficArea);
            $dateTimeProcessor = new AddDaysExcludingDates($dateTimeProcessor, $dateProvider);
        }

        return $dateTimeProcessor;
    }
}
