<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * HintQuestion Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="hint_question",
 *    indexes={
 *        @ORM\Index(name="ix_hint_question_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_hint_question_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class HintQuestion extends AbstractHintQuestion
{

}
