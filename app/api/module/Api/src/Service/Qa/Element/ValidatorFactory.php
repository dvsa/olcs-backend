<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class ValidatorFactory
{
    /**
     * Create and return a ValidatorFactory instance
     *
     * @param string $rule
     * @param array $parameters
     *
     * @return Validator
     */
    public function create($rule, array $parameters)
    {
        return new Validator($rule, $parameters);
    }
}
