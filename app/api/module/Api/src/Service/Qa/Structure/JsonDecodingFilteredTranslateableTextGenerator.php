<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

class JsonDecodingFilteredTranslateableTextGenerator
{
    /** @var FilteredTranslateableTextGenerator */
    private $filteredTranslateableTextGenerator;

    /**
     * Create service instance
     *
     * @param FilteredTranslateableTextGenerator $filteredTranslateableTextGenerator
     *
     * @return JsonDecodingFilteredTranslateableTextGenerator
     */
    public function __construct(FilteredTranslateableTextGenerator $filteredTranslateableTextGenerator)
    {
        $this->filteredTranslateableTextGenerator = $filteredTranslateableTextGenerator;
    }

    /**
     * Build and return a FilteredTranslateableTextGenerator from the supplied value if the value is a string,
     * otherwise return null
     *
     * @param string|null $value
     *
     * @return FilteredTranslateableText|null
     */
    public function generate($value)
    {
        if (is_string($value)) {
            return $this->filteredTranslateableTextGenerator->generate(
                json_decode($value, true)
            );
        }

        return null;
    }
}
