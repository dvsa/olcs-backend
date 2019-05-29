<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationValidation;

class ValidatorGenerator
{
    /** @var ValidatorFactory */
    private $validatorFactory;

    /**
     * Create service instance
     *
     * @param ValidatorFactory $validatorFactory
     *
     * @return ValidatorGenerator
     */
    public function __construct(ValidatorFactory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Build and return a Validator instance using the appropriate data sources
     *
     * @param ApplicationValidation $applicationValidation
     *
     * @return Validator
     */
    public function generate(ApplicationValidation $applicationValidation)
    {
        return $this->validatorFactory->create(
            $applicationValidation->getRule(),
            json_decode(
                $applicationValidation->getParameters(),
                true
            )
        );
    }
}
