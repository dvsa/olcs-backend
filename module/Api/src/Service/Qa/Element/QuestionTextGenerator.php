<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\QuestionText as QuestionTextEntity;

class QuestionTextGenerator
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
     * Build and return an QuestionText instance using the appropriate data sources
     *
     * @param QuestionTextEntity $questionTextEntity
     *
     * @return QuestionText
     */
    public function generate(QuestionTextEntity $questionTextEntity)
    {
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
