<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextGenerator;

class TextGenerator implements ElementGeneratorInterface
{
    /** @var TextFactory */
    private $textFactory;

    /** @var TranslateableTextGenerator */
    private $translateableTextGenerator;

    /**
     * Create service instance
     *
     * @param TextFactory $textFactory
     * @param TranslateableTextGenerator $translateableTextGenerator
     *
     * @return Text
     */
    public function __construct(
        TextFactory $textFactory,
        TranslateableTextGenerator $translateableTextGenerator
    ) {
        $this->textFactory = $textFactory;
        $this->translateableTextGenerator = $translateableTextGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $applicationStepEntity = $context->getApplicationStepEntity();
        $irhpApplicationEntity = $context->getIrhpApplicationEntity();

        $options = $context->getApplicationStepEntity()->getDecodedOptionSource();

        $label = null;
        if (isset($options['label'])) {
            $label = $this->translateableTextGenerator->generate($options['label']);
        }

        $hint = null;
        if (isset($options['hint'])) {
            $hint = $this->translateableTextGenerator->generate($options['hint']);
        }

        return $this->textFactory->create(
            $label,
            $hint,
            $irhpApplicationEntity->getAnswer($applicationStepEntity)
        );
    }
}
