<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

class TranslateableTextGenerator
{
    /** @var TranslateableTextFactory */
    private $translateableTextFactory;

    /**
     * Create service instance
     *
     * @param TranslateableTextFactory $translateableTextFactory
     *
     * @return TranslateableTextGenerator
     */
    public function __construct(TranslateableTextFactory $translateableTextFactory)
    {
        $this->translateableTextFactory = $translateableTextFactory;
    }

    /**
     * Build and return a TranslateableText instance using the appropriate data sources
     *
     * @param array $options
     *
     * @return TranslateableText
     */
    public function generate(array $options)
    {
        $translateableText = $this->translateableTextFactory->create($options['key']);

        if (isset($options['parameters'])) {
            foreach ($options['parameters'] as $parameter) {
                $translateableText->addParameter($parameter);
            }
        }

        return $translateableText;
    }
}
