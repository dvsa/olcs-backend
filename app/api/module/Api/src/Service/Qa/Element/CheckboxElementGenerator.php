<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class CheckboxElementGenerator implements ElementGeneratorInterface
{
    /** @var CheckboxElementFactory */
    private $checkboxElementFactory;

    /** @var TranslateableTextGenerator */
    private $translateableTextGenerator;

    /**
     * Create service instance
     *
     * @param CheckboxElementFactory $checkboxElementFactory
     * @param TranslateableTextGenerator $translateableTextGenerator
     *
     * @return CheckboxElement
     */
    public function __construct(
        CheckboxElementFactory $checkboxElementFactory,
        TranslateableTextGenerator $translateableTextGenerator
    ) {
        $this->checkboxElementFactory = $checkboxElementFactory;
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

        $answerValue = false;
        if (!is_null($answerEntity)) {
            $answerValue = ($answerEntity->getValue() === true);
        }

        return $this->checkboxElementFactory->create(
            $this->translateableTextGenerator->generate($options['label']),
            $this->translateableTextGenerator->generate($options['notCheckedMessage']),
            $answerValue
        );
    }
}
