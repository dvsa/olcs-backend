<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Service\Qa\Structure\JsonDecodingFilteredTranslateableTextGenerator;

class QuestionTextGenerator implements QuestionTextGeneratorInterface
{
    /** @var QuestionTextFactory */
    private $questionTextFactory;

    /** @var JsonDecodingFilteredTranslateableTextGenerator */
    private $jsonDecodingFilteredTranslateableTextGenerator;

    /**
     * Create service instance
     *
     * @param QuestionTextFactory $questionTextFactory
     * @param JsonDecodingFilteredTranslateableTextGenerator $jsonDecodingFilteredTranslateableTextGenerator
     *
     * @return QuestionTextGenerator
     */
    public function __construct(
        QuestionTextFactory $questionTextFactory,
        JsonDecodingFilteredTranslateableTextGenerator $jsonDecodingFilteredTranslateableTextGenerator
    ) {
        $this->questionTextFactory = $questionTextFactory;
        $this->jsonDecodingFilteredTranslateableTextGenerator = $jsonDecodingFilteredTranslateableTextGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(QuestionTextGeneratorContext $context)
    {
        $questionTextEntity = $context->getApplicationStepEntity()->getQuestion()->getActiveQuestionText();

        return $this->questionTextFactory->create(
            $this->jsonDecodingFilteredTranslateableTextGenerator->generate(
                $questionTextEntity->getQuestionKey()
            ),
            $this->jsonDecodingFilteredTranslateableTextGenerator->generate(
                $questionTextEntity->getDetailsKey()
            ),
            $this->jsonDecodingFilteredTranslateableTextGenerator->generate(
                $questionTextEntity->getGuidanceKey()
            ),
            $this->jsonDecodingFilteredTranslateableTextGenerator->generate(
                $questionTextEntity->getAdditionalGuidanceKey()
            )
        );
    }
}
