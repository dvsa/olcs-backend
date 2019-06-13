<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

class FilteredTranslateableTextFactory
{
    /**
     * Create and return an FilteredTranslateableText instance
     *
     * @param string $filter
     * @param TranslateableText $translateableText
     *
     * @return FilteredTranslateableText
     */
    public function create($filter, TranslateableText $translateableText)
    {
        return new FilteredTranslateableText($filter, $translateableText);
    }
}
