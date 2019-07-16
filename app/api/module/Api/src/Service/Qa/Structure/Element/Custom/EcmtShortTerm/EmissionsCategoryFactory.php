<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

class EmissionsCategoryFactory
{
    /**
     * Create and return an EmissionsCategory instance
     *
     * @param string $name
     * @param string $labelTranslationKey
     * @param int|null $value
     * @param int $maxValue
     *
     * @return EmissionsCategory
     */
    public function create($name, $labelTranslationKey, $value, $maxValue)
    {
        return new EmissionsCategory($name, $labelTranslationKey, $value, $maxValue);
    }
}
