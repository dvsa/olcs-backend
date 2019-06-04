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
}
