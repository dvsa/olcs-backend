<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use RuntimeException;

class TypeBasedRestrictedCountriesProvider
{
    /** @var array */
    private $config;

    /**
     * Create service instance
     *
     * @param array $config
     *
     * @return TypeBasedRestrictedCountriesProvider
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Return the restricted country codes corresponding to the specified IrhpPermitType
     *
     * @param int $irhpPermitTypeId
     *
     * @return array
     */
    public function getIds($irhpPermitTypeId)
    {
        $typesConfig = $this->config['permits']['types'];

        if (!isset($typesConfig[$irhpPermitTypeId]['restricted_countries'])) {
            throw new RuntimeException('No restricted countries config found for permit type ' . $irhpPermitTypeId);
        }

        return $typesConfig[$irhpPermitTypeId]['restricted_countries'];
    }
}
