<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

class RestrictedCountryIdsProvider
{
    /** @var array */
    private $restrictedCountryIds;

    /**
     * Create service instance
     *
     * @param array $restrictedCountryIds
     *
     * @return RestrictedCountryIdsProvider
     */
    public function __construct(array $restrictedCountryIds)
    {
        $this->restrictedCountryIds = $restrictedCountryIds;
    }

    /**
     * Return a list of ids representing the restricted countries
     *
     * @return array
     */
    public function getIds()
    {
        return $this->restrictedCountryIds;
    }
}
