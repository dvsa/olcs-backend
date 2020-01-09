<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use RuntimeException;

class RadioAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    /** @var OptionsGenerator */
    private $optionsGenerator;

    /**
     * Create service instance
     *
     * @param OptionsGenerator $optionsGenerator
     *
     * @return RadioAnswerSummaryProvider
     */
    public function __construct(OptionsGenerator $optionsGenerator)
    {
        $this->optionsGenerator = $optionsGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'generic';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVariables(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        $isSnapshot
    ) {
        $options = $applicationStepEntity->getDecodedOptionSource();

        $radioOptions = $this->optionsGenerator->generate($options['source']);
        $answerValue = $irhpApplicationEntity->getAnswer($applicationStepEntity);

        foreach ($radioOptions as $radioOption) {
            if ($radioOption['value'] == $answerValue) {
                $answerLabel = $radioOption['label'];
                return ['answer' => $answerLabel];
            }
        }

        throw new RuntimeException('Answer not found in list of options');
    }
}
