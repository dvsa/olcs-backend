<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

class PermitTypeConfig
{
    /** @var string */
    private $restrictedCountriesQuestionKey;

    /** @var array */
    private $restrictedCountryIds;

    /**
     * Create instance
     *
     * @param string $restrictedCountriesQuestionKey
     * @param array $restrictedCountryIds
     *
     * @return PermitTypeConfig
     */
    public function __construct($restrictedCountriesQuestionKey, array $restrictedCountryIds)
    {
        $this->restrictedCountriesQuestionKey = $restrictedCountriesQuestionKey;
        $this->restrictedCountryIds = $restrictedCountryIds;
    }

    /**
     * Return the restricted countries question translation key
     *
     * @return string
     */
    public function getRestrictedCountriesQuestionKey()
    {
        return $this->restrictedCountriesQuestionKey;
    }

    /**
     * Return the restricted country ids
     *
     * @return array
     */
    public function getRestrictedCountryIds()
    {
        return $this->restrictedCountryIds;
    }
}
