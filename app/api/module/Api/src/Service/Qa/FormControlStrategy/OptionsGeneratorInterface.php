<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

interface OptionsGeneratorInterface
{
    /**
     * Provides a list of options for radio buttons, checkboxes etc, using the provided parameters to determine the
     * source of the options
     *
     * @param array $parameters
     */
    public function get(array $parameters);
}
