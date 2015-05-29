<?php

namespace Dvsa\Olcs\Api\Entity\Submission;

use Doctrine\ORM\Mapping as ORM;

/**
 * Submission Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="submission",
 *    indexes={
 *        @ORM\Index(name="ix_submission_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_submission_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_submission_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_submission_submission_type", columns={"submission_type"})
 *    }
 * )
 */
class Submission extends AbstractSubmission
{

}
