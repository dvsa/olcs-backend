<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\ORM\Mapping as ORM;

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
