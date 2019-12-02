<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

interface QuestionTextGeneratorInterface
{
    /**
     * Build and return an QuestionText instance using the appropriate data sources
     *
     * @param QuestionTextGeneratorContext $questionTextGeneratorContext
     *
     * @return QuestionText
     */
    public function generate(QuestionTextGeneratorContext $context);
}
