<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

class TranslateableTextParameterGenerator
{
    /** @var TranslateableTextParameterFactory */
    private $translateableTextParameterFactory;

    /**
     * Create service instance
     *
     * @param TranslateableTextParameterFactory $translateableTextFactory
     *
     * @return TranslateableTextParameterGenerator
     */
    public function __construct(TranslateableTextParameterFactory $translateableTextParameterFactory)
    {
        $this->translateableTextParameterFactory = $translateableTextParameterFactory;
    }

    /**
     * Build and return a TranslateableTextParameter instance using the appropriate data sources
     *
     * @param array $options
     *
     * @return TranslateableTextParameter
     */
    public function generate(array $options)
    {
        $formatter = null;
        if (isset($options['formatter'])) {
            $formatter = $options['formatter'];
        }

        return $this->translateableTextParameterFactory->create($options['value'], $formatter);
    }
}
