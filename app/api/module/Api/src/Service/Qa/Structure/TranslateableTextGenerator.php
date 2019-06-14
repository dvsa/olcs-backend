<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

class TranslateableTextGenerator
{
    /** @var TranslateableTextFactory */
    private $translateableTextFactory;

    /** @var TranslateableTextParameterGenerator */
    private $translateableTextParameterGenerator;

    /**
     * Create service instance
     *
     * @param TranslateableTextFactory $translateableTextFactory
     * @param TranslateableTextParameterGenerator $translateableTextParameterGenerator
     *
     * @return TranslateableTextGenerator
     */
    public function __construct(
        TranslateableTextFactory $translateableTextFactory,
        TranslateableTextParameterGenerator $translateableTextParameterGenerator
    ) {
        $this->translateableTextFactory = $translateableTextFactory;
        $this->translateableTextParameterGenerator = $translateableTextParameterGenerator;
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
                $translateableText->addParameter(
                    $this->translateableTextParameterGenerator->generate($parameter)
                );
            }
        }

        return $translateableText;
    }
}
