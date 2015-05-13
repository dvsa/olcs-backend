<?php

namespace Dvsa\Olcs\Api\Entity\Submission;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubmissionSectionComment Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="submission_section_comment",
 *    indexes={
 *        @ORM\Index(name="ix_submission_section_comment_submission_id", columns={"submission_id"}),
 *        @ORM\Index(name="ix_submission_section_comment_submission_section", columns={"submission_section"}),
 *        @ORM\Index(name="ix_submission_section_comment_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_submission_section_comment_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SubmissionSectionComment extends AbstractSubmissionSectionComment
{

}
