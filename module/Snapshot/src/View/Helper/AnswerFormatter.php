<?php

namespace Dvsa\Olcs\Snapshot\View\Helper;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Laminas\View\Helper\AbstractHelper;

/**
 * Format data passed in q&a format
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 * TODO: this class will become redundant once ECMT migrates over to unified IRHP snapshot/check answers mechanism
 */
class AnswerFormatter extends AbstractHelper
{
    const SEPARATOR = '<br/>';

    /**
     * Expects a Q&A answer in the form of $data['question'], $data['answer'] etc
     * Basic for now, will return a formatted/translated answer based on the question type
     * For future, add support for external formatters
     *
     * @param array $data answer data
     *
     * @return string
     */
    public function __invoke(array $data): string
    {
        $answers = [];

        if (!is_array($data['answer'])) {
            $data['answer'] = (array)$data['answer'];
        }

        $escape = isset($data['escape']) ? $data['escape'] : true;

        foreach ($data['answer'] as $answer) {
            switch ($data['questionType']) {
                case Question::QUESTION_TYPE_BOOLEAN:
                    $answers[] = $this->translateAndEscape(
                        $this->formatBoolean($answer),
                        $escape
                    );
                    break;
                case Question::QUESTION_TYPE_INTEGER:
                    $answers[] = (int) $answer;
                    break;
                default:
                    $answers[] = $this->translateAndEscape($answer, $escape);
            }
        }

        return implode(self::SEPARATOR, $answers);
    }

    /**
     * Translate and escape the answer
     *
     * @param string $answer the answer
     * @param bool   $escape the answer
     *
     * @return string
     */
    private function translateAndEscape($answer, bool $escape): string
    {
        $translatedAnswer = $this->view->translate($answer);

        if ($escape) {
            return $this->view->escapeHtml($translatedAnswer);
        }

        return $translatedAnswer;
    }

    /**
     * Format a truthy/falsy value as a string value of Yes or No
     *
     * @param mixed $answer
     *
     * @return string
     */
    private function formatBoolean($answer)
    {
        if (!$answer) {
            return 'No';
        }

        return 'Yes';
    }
}
