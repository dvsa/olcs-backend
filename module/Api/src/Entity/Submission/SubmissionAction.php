<?php

namespace Dvsa\Olcs\Api\Entity\Submission;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubmissionAction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="submission_action",
 *    indexes={
 *        @ORM\Index(name="ix_submission_action_sender_user_id", columns={"sender_user_id"}),
 *        @ORM\Index(name="ix_submission_action_recipient_user_id", columns={"recipient_user_id"}),
 *        @ORM\Index(name="ix_submission_action_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_submission_action_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_submission_action_submission_id", columns={"submission_id"}),
 *        @ORM\Index(name="ix_submission_action_submission_action_status", columns={"submission_action_status"})
 *    }
 * )
 */
class SubmissionAction extends AbstractSubmissionAction
{

}
