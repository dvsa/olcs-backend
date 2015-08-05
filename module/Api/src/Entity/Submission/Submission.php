<?php

namespace Dvsa\Olcs\Api\Entity\Submission;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;

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
    public function __construct(CasesEntity $case, RefData $submissionType, $dataSnapshot = '')
    {
        parent::__construct();

        $this->setCase($case);
        $this->setSubmissionType($submissionType);
        $this->setDataSnapshot($dataSnapshot);
    }
}
