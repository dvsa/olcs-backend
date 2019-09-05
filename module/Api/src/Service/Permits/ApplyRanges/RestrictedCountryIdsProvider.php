<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

class RestrictedCountryIdsProvider
{
    /**
     * Populate a list of ids representing the restricted countries
     *
     * @return array
     */
    public function getIds()
    {
        return ['AT', 'GR', 'HU', 'IT', 'RU'];
    }
}
