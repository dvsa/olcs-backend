<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextGenerator;

class CheckboxGenerator implements ElementGeneratorInterface
{
    /** @var CheckboxFactory */
    private $checkboxFactory;

    /** @var TranslateableTextGenerator */
    private $translateableTextGenerator;

    /**
     * Create service instance
     *
     * @param CheckboxFactory $checkboxFactory
     * @param TranslateableTextGenerator $translateableTextGenerator
     *
     * @return CheckboxGenerator
     */
    public function __construct(
        CheckboxFactory $checkboxFactory,
        TranslateableTextGenerator $translateableTextGenerator
    ) {
        $this->checkboxFactory = $checkboxFactory;
        $this->translateableTextGenerator = $translateableTextGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $applicationStepEntity = $context->getApplicationStepEntity();
        $irhpApplicationEntity = $context->getIrhpApplicationEntity();

        $options = $applicationStepEntity->getDecodedOptionSource();

        $checked = false;
        $answerValue = $irhpApplicationEntity->getAnswer($applicationStepEntity);
        if (!is_null($answerValue)) {
            $checked = ($answerValue === true);
        }

        return $this->checkboxFactory->create(
            $this->translateableTextGenerator->generate($options['label']),
            $this->translateableTextGenerator->generate($options['notCheckedMessage']),
            $checked
        );
    }
}
