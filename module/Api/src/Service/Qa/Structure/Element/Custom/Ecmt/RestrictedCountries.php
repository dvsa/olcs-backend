<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class RestrictedCountries implements ElementInterface
{
    /** @var bool|null */
    private $yesNo;

    /** @var string */
    private $questionKey;

    /** @var array */
    private $restrictedCountries = [];

    /**
     * Create instance
     *
     * @param bool|null $yesNo
     * @param string $questionKey
     *
     * @return RestrictedCountries
     */
    public function __construct($yesNo, $questionKey)
    {
        $this->yesNo = $yesNo;
        $this->questionKey = $questionKey;
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
            'questionKey' => $this->questionKey,
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
