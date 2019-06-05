<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class TextElementFactory
{
    /**
     * Create and return a TextElement instance
     *
     * @param TranslateableText $label
     * @param TranslateableText $hint (optional)
     * @param string $value
     *
     * @return TextElement
     */
    public function create(TranslateableText $label, ?TranslateableText $hint = null, $value)
    {
        return new TextElement($label, $hint, $value);
    }
}
