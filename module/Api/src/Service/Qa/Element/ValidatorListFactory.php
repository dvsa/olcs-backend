<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class ValidatorListFactory
{
    /**
     * Create and return a ValidatorList instance
     *
     * @return ValidatorList
     */
    public function create()
    {
        return new ValidatorList();
    }
}
