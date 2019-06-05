<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class TextElementGenerator implements ElementGeneratorInterface
{
    /** @var TextElementFactory */
    private $textElementFactory;

    /** @var TranslateableTextGenerator */
    private $translateableTextGenerator;

    /**
     * Create service instance
     *
     * @param TextElementFactory $textElementFactory
     * @param TranslateableTextGenerator $translateableTextGenerator
     *
     * @return TextElement
     */
    public function __construct(
        TextElementFactory $textElementFactory,
        TranslateableTextGenerator $translateableTextGenerator
    ) {
        $this->textElementFactory = $textElementFactory;
        $this->translateableTextGenerator = $translateableTextGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        ?AnswerEntity $answerEntity = null
    ) {
        $options = $applicationStepEntity->getDecodedOptionSource();

        $hint = null;
        if (isset($options['hint'])) {
            $hint = $this->translateableTextGenerator->generate($options['hint']);
        }

        $answerValue = null;
        if (!is_null($answerEntity)) {
            $answerValue = $answerEntity->getValue();
        }

        return $this->textElementFactory->create(
            $this->translateableTextGenerator->generate($options['label']),
            $hint,
            $answerValue
        );
    }
}
