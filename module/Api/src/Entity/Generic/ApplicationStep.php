<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationStep Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application_step",
 *    indexes={
 *        @ORM\Index(name="fk_application_path_steps_application_paths1_idx",
     *     columns={"application_path_id"}),
 *        @ORM\Index(name="fk_application_path_steps_questions1_idx", columns={"question_id"}),
 *        @ORM\Index(name="fk_application_step_application_step1_idx", columns={"parent_id"})
 *    }
 * )
 */
class ApplicationStep extends AbstractApplicationStep
{

}
