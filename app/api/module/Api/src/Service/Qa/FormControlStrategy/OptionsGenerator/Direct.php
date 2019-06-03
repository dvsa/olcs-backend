<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\OptionsGenerator;

use Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\OptionsGeneratorInterface;

class Direct implements OptionsGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(array $parameters)
    {
        return $parameters;
    }
}
