<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class ApplicationUpdater
{
    /** @var ApplicationCountryUpdater */
    private $applicationCountryUpdater;

    /**
     * Create service instance
     *
     * @param ApplicationCountryUpdater $applicationCountryUpdater
     *
     * @return ApplicationUpdater
     */
    public function __construct(ApplicationCountryUpdater $applicationCountryUpdater)
    {
        $this->applicationCountryUpdater = $applicationCountryUpdater;
    }

    /**
     * Update the application using the supplied fields data
     *
     * @param IrhpApplication $irhpApplication
     * @param array $countries
     */
    public function update(IrhpApplication $irhpApplication, array $countries)
    {
        foreach ($countries as $countryId => $periodData) {
            $selectedStockId = $periodData['periodId'];
            $requiredPermits = $periodData['permitsRequired'];

            $this->applicationCountryUpdater->update(
                $irhpApplication,
                $countryId,
                $selectedStockId,
                $requiredPermits
            );
        }
    }
}
