<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionText Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="question_text",
 *    indexes={
 *        @ORM\Index(name="fk_question_text_questions_idx", columns={"question_id"})
 *    }
 * )
 */
class QuestionText extends AbstractQuestionText
{
    /**
     * Get an array representing the non-form elements displayed within an application step
     *
     * @return array
     */
    public function getTemplateVars()
    {
        return [
            'question' => $this->questionKey,
            'details' => $this->detailsKey,
            'guidance' => json_decode($this->guidanceKey, true),
            'additionalGuidance' => $this->additionalGuidanceKey,
        ];
    }
}
