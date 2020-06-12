<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class CountryGenerator
{
    /** @var PeriodArrayGenerator */
    private $periodArrayGenerator;

    /**
     * Create service instance
     *
     * @param PeriodArrayGenerator $periodArrayGenerator
     *
     * @return CountryGenerator
     */
    public function __construct(PeriodArrayGenerator $periodArrayGenerator)
    {
        $this->periodArrayGenerator = $periodArrayGenerator;
    }

    /**
     * Generate the country part of the response
     *
     * @param Country $country
     * @param IrhpApplication $irhpApplication
     *
     * @return array
     */
    public function generate(Country $country, IrhpApplication $irhpApplication)
    {
        $countryId = $country->getId();
        $selectedPeriodId = null;

        $irhpPermitApplication = $irhpApplication->getIrhpPermitApplicationByStockCountryId($countryId);
        if (is_object($irhpPermitApplication)) {
            $selectedPeriodId = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getId();
        }

        return [
            'id' => $countryId,
            'name' => $country->getCountryDesc(),
            'visible' => $irhpApplication->hasCountryId($countryId),
            'selectedPeriodId' => $selectedPeriodId,
            'periods' => $this->periodArrayGenerator->generate($country, $irhpPermitApplication),
        ];
    }
}
