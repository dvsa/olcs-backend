<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class CheckboxElementFactory
{
    /**
     * Create and return a Checkbox instance
     *
     * @param TranslateableText $label
     * @param TranslateableText $notCheckedMessage
     * @param bool $checked
     *
     * @return CheckboxElement
     */
    public function create(TranslateableText $label, TranslateableText $notCheckedMessage, $checked)
    {
        return new CheckboxElement($label, $notCheckedMessage, $checked);
    }
}
