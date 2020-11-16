<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class CountryGenerator
{
    const PERIOD_LABELS = [
        Behaviour::STANDARD => 'Select period',
        Behaviour::MOROCCO => 'Select stock',
    ];

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

        $behaviour = Behaviour::STANDARD;
        if ($country->isMorocco()) {
            $behaviour = Behaviour::MOROCCO;
        }

        return [
            'id' => $countryId,
            'name' => $country->getCountryDesc(),
            'type' => $behaviour,
            'visible' => $irhpApplication->hasCountryId($countryId),
            'selectedPeriodId' => $selectedPeriodId,
            'periodLabel' => self::PERIOD_LABELS[$behaviour],
            'periods' => $this->periodArrayGenerator->generate($behaviour, $country, $irhpPermitApplication),
        ];
    }
}
