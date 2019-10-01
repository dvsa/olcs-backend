<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class RestrictedCountries implements ElementInterface
{
    /** @var bool|null */
    private $yesNo;

    /** @var array */
    private $restrictedCountries = [];

    /**
     * Create instance
     *
     * @param bool|null $yesNo
     *
     * @return RestrictedCountries
     */
    public function __construct($yesNo)
    {
        $this->yesNo = $yesNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        $restrictedCountriesRepresentations = [];

        foreach ($this->restrictedCountries as $restrictedCountry) {
            $restrictedCountriesRepresentations[] = $restrictedCountry->getRepresentation();
        }

        return [
            'yesNo' => $this->yesNo,
            'countries' => $restrictedCountriesRepresentations
        ];
    }

    /**
     * Add a restricted country to the representation
     *
     * @param RestrictedCountry $restrictedCountry
     */
    public function addRestrictedCountry(RestrictedCountry $restrictedCountry)
    {
        $this->restrictedCountries[] = $restrictedCountry;
    }
}
