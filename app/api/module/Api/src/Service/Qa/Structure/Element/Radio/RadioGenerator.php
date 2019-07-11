<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextGenerator;

class RadioGenerator implements ElementGeneratorInterface
{
    /** @var RadioFactory */
    private $radioFactory;

    /** @var OptionsGenerator */
    private $optionsGenerator;

    /** @var TranslateableTextGenerator */
    private $translateableTextGenerator;

    /**
     * Create service instance
     *
     * @param RadioFactory $radioFactory
     * @param OptionsGenerator $optionsGenerator
     * @param TranslateableTextGenerator $translateableTextGenerator
     *
     * @return RadioGenerator
     */
    public function __construct(
        RadioFactory $radioFactory,
        OptionsGenerator $optionsGenerator,
        TranslateableTextGenerator $translateableTextGenerator
    ) {
        $this->radioFactory = $radioFactory;
        $this->optionsGenerator = $optionsGenerator;
        $this->translateableTextGenerator = $translateableTextGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $applicationStepEntity = $context->getApplicationStepEntity();
        $options = $applicationStepEntity->getDecodedOptionSource();

        return $this->radioFactory->create(
            $this->optionsGenerator->generate($options['source']),
            $this->translateableTextGenerator->generate($options['notSelectedMessage']),
            $context->getIrhpApplicationEntity()->getAnswer($applicationStepEntity)
        );
    }
}
