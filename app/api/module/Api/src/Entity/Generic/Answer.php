<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

/**
 * Answer Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="answer",
 *    indexes={
 *        @ORM\Index(name="fk_answers_question_text1_idx", columns={"question_text_id"}),
 *        @ORM\Index(name="fk_application_path_irhp_permit_application_id",
     *     columns={"irhp_permit_application_id"})
 *    }
 * )
 */
class Answer extends AbstractAnswer
{
    /**
     * Create a new instance for use against an IRHP application
     *
     * @param QuestionText $questionText
     * @param IrhpApplication $irhpApplication
     *
     * @return Answer
     */
    public static function createNewForIrhpApplication(QuestionText $questionText, IrhpApplication $irhpApplication)
    {
        $answer = new self();
        $answer->questionText = $questionText;
        $answer->irhpApplication = $irhpApplication;

        return $answer;
    }

    /**
     * Store the answer using the provided type and value
     *
     * @param string $questionType
     * @param mixed $answerValue
     *
     * @return bool
     */
    public function setValue($questionType, $answerValue)
    {
        $this->ansArray = null;
        $this->ansBoolean = null;
        $this->ansDate = null;
        $this->ansDatetime = null;
        $this->ansDecimal = null;
        $this->ansInteger = null;
        $this->ansString = null;
        $this->ansText = null;

        switch ($questionType) {
            case Question::QUESTION_TYPE_STRING:
                $this->ansString = $answerValue;
                break;
            case Question::QUESTION_TYPE_INTEGER:
                $this->ansInteger = $answerValue;
                break;
            case Question::QUESTION_TYPE_BOOLEAN:
                $this->ansBoolean = $answerValue;
                break;
            case Question::QUESTION_TYPE_DATE:
                $this->ansDate = $answerValue;
                break;
            default:
                throw new RuntimeException('The ' . $questionType . ' type is not yet supported');
        }
    }

    /**
     * Whether the value of this answer is equal to the provided value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEqualTo($value)
    {
        $fieldValue = $this->getValue();

        if (!is_null($fieldValue)) {
            return $fieldValue == $value;
        }

        throw new RuntimeException('No non-null field values found in answer');
    }

    /**
     * Get the answer value
     *
     * @return mix|null
     */
    public function getValue()
    {
        $fieldValues = [
            $this->ansArray,
            $this->ansBoolean,
            $this->ansDate,
            $this->ansDatetime,
            $this->ansDecimal,
            $this->ansInteger,
            $this->ansString,
            $this->ansText
        ];

        foreach ($fieldValues as $fieldValue) {
            if (!is_null($fieldValue)) {
                return $fieldValue;
            }
        }

        return null;
    }
}
