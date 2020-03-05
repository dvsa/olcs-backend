<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class GenericAnswerSummaryProvider implements AnswerSummaryProviderInterface
{
    use AnyTrait;

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
        return ['answer' => $qaContext->getAnswerValue()];
    }
}
