<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class ApplicationStepFactory
{
    /**
     * Create and return an ApplicationStep instance
     *
     * @param string $type
     * @param string $fieldsetName
     * @param string $shortName
     * @param string $slug
     * @param ElementInterface $element
     * @param ValidatorList $validatorList
     *
     * @return ApplicationStep
     */
    public function create(
        $type,
        $fieldsetName,
        $shortName,
        $slug,
        ElementInterface $element,
        ValidatorList $validatorList
    ) {
        return new ApplicationStep($type, $fieldsetName, $shortName, $slug, $element, $validatorList);
    }
}
