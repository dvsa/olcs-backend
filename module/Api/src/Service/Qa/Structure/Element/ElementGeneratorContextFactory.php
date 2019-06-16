<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;

class ElementGeneratorContextFactory
{
    /**
     * Create and return an ElementGeneratorContext instance
     *
     * @param ValidatorList $validatorList
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return ElementGeneratorContext
     */
    public function create(
        ValidatorList $validatorList,
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity
    ) {
        return new ElementGeneratorContext($validatorList, $applicationStepEntity, $irhpApplicationEntity);
    }
}
