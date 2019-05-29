<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class TranslateableTextFactory
{
    /**
     * Create and return a TranslateableText instance
     *
     * @param string $key
     *
     * @return TranslateableText
     */
    public function create($key)
    {
        return new TranslateableText($key);
    }
}
