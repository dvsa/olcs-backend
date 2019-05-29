<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;

class ValidatorListGenerator
{
    /** @var ValidatorListFactory */
    private $validatorListFactory;

    /** @var ValidatorGenerator */
    private $validatorGenerator;

    /**
     * Create service instance
     *
     * @param ValidatorListFactory $validatorListFactory
     * @param ValidatorGenerator $validatorGenerator
     *
     * @return ValidatorListGenerator
     */
    public function __construct(
        ValidatorListFactory $validatorListFactory,
        ValidatorGenerator $validatorGenerator
    ) {
        $this->validatorListFactory = $validatorListFactory;
        $this->validatorGenerator = $validatorGenerator;
    }

    /**
     * Build and return a ValidatorList instance using the appropriate data sources
     *
     * @param ApplicationStepEntity $applicationStepEntity
     *
     * @return ValidatorList
     */
    public function generate(ApplicationStepEntity $applicationStepEntity)
    {
        $validatorList = $this->validatorListFactory->create();
        $applicationValidations = $applicationStepEntity->getQuestion()->getApplicationValidations();

        foreach ($applicationValidations as $applicationValidation) {
            $validatorList->addValidator(
                $this->validatorGenerator->generate($applicationValidation)
            );
        }

        return $validatorList;
    }
}
