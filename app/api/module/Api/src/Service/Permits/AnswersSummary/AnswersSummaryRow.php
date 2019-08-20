<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

class AnswersSummaryRow
{
    /** @var string */
    private $question;

    /** @var string */
    private $formattedAnswer;

    /** @var string|null */
    private $slug;

    /**
     * Create instance
     *
     * @param string $question
     * @param string $formattedAnswer
     * @param string|null $slug
     *
     * @return AnswersSummaryRow
     */
    public function __construct($question, $formattedAnswer, $slug = null)
    {
        $this->question = $question;
        $this->formattedAnswer = $formattedAnswer;
        $this->slug = $slug;
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'question' => $this->question,
            'answer' => $this->formattedAnswer,
            'slug' => $this->slug,
        ];
    }
}
