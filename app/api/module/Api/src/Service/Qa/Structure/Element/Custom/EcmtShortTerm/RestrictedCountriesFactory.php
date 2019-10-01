<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

class RestrictedCountriesFactory
{
    /**
     * Create and return a RestrictedCountries instance
     *
     * @param bool|null $yesNo
     *
     * @return RestrictedCountries
     */
    public function create($yesNo)
    {
        return new RestrictedCountries($yesNo);
    }
}
