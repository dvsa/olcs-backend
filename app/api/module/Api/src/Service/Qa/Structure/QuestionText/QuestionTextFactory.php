<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Service\Qa\Structure\FilteredTranslateableText;

class QuestionTextFactory
{
    /**
     * Create and return a QuestionText instance
     *
     * @param FilteredTranslateableText $question (optional)
     * @param FilteredTranslateableText $details (optional)
     * @param FilteredTranslateableText $guidance (optional)
     * @param FilteredTranslateableText $additionalGuidance (optional)
     *
     * @return QuestionText
     */
    public function create(
        ?FilteredTranslateableText $question = null,
        ?FilteredTranslateableText $details = null,
        ?FilteredTranslateableText $guidance = null,
        ?FilteredTranslateableText $additionalGuidance = null
    ) {
        return new QuestionText($question, $details, $guidance, $additionalGuidance);
    }
}
