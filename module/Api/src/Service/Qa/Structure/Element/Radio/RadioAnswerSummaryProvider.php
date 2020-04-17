<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AlwaysIncludeSlugTrait;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;
use RuntimeException;

class RadioAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    use AlwaysIncludeSlugTrait, AnyTrait;

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
    public function getTemplateVariables(QaContext $qaContext, $isSnapshot)
    {
        $options = $qaContext->getApplicationStepEntity()->getDecodedOptionSource();

        $radioOptions = $this->optionsGenerator->generate($options['source']);
        $answerValue = $qaContext->getAnswerValue();

        foreach ($radioOptions as $radioOption) {
            if ($radioOption['value'] == $answerValue) {
                $answerLabel = $radioOption['label'];
                return ['answer' => $answerLabel];
            }
        }

        throw new RuntimeException('Answer not found in list of options');
    }
}
