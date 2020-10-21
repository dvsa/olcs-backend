<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use RuntimeException;

class TypeBasedPermitTypeConfigProvider
{
    /** @var array */
    private $config;

    /**
     * Create service instance
     *
     * @param array $config
     *
     * @return TypeBasedPermitTypeConfigProvider
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Return the restricted country codes corresponding to the specified IrhpPermitType
     *
     * @param int $irhpPermitTypeId
     * @param array $excludedRestrictedCountryIds
     *
     * @return PermitTypeConfig
     *
     * @throws RuntimeException
     */
    public function getPermitTypeConfig($irhpPermitTypeId, $excludedRestrictedCountryIds = [])
    {
        $typesConfig = $this->config['permits']['types'];

        if (!isset($typesConfig[$irhpPermitTypeId])) {
            throw new RuntimeException('No config found for permit type ' . $irhpPermitTypeId);
        }

        $typeConfig = $typesConfig[$irhpPermitTypeId];

        $restrictedCountryIds = array_diff($typeConfig['restricted_country_ids'], $excludedRestrictedCountryIds);

        return new PermitTypeConfig(
            $typeConfig['restricted_countries_question_key'],
            $restrictedCountryIds
        );
    }
}
