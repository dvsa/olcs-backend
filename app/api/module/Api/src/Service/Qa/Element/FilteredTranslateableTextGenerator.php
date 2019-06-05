<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class FilteredTranslateableTextGenerator
{
    /** @var FilteredTranslateableTextFactory */
    private $filteredTranslateableTextFactory;

    /** @var TranslateableTextGenerator */
    private $translateableTextGenerator;

    /**
     * Create service instance
     *
     * @param FilteredTranslateableTextFactory $filteredTranslateableTextFactory
     * @param TranslateableTextGenerator $translateableTextGenerator
     *
     * @return FilteredTranslateableText
     */
    public function __construct(
        FilteredTranslateableTextFactory $filteredTranslateableTextFactory,
        TranslateableTextGenerator $translateableTextGenerator
    ) {
        $this->filteredTranslateableTextFactory = $filteredTranslateableTextFactory;
        $this->translateableTextGenerator = $translateableTextGenerator;
    }

    /**
     * Build and return an FilteredTranslateableText instance using the appropriate data sources
     *
     * @param array $options
     *
     * @return FilteredTranslateableText
     */
    public function generate(array $options)
    {
        return $this->filteredTranslateableTextFactory->create(
            $options['filter'],
            $this->translateableTextGenerator->generate($options['translateableText'])
        );
    }
}
