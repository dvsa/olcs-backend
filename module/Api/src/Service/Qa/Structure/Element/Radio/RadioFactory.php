<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;

class RadioFactory
{
    /**
     * Create and return a Radio instance
     *
     * @param array $options
     * @param TranslateableText $notSelectedMessage
     * @param mixed $value
     *
     * @return Radio
     */
    public function create(array $options, TranslateableText $notSelectedMessage, $value)
    {
        return new Radio($options, $notSelectedMessage, $value);
    }
}
