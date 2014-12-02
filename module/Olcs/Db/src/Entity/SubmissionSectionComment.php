<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SubmissionSectionComment Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission_section_comment",
 *    indexes={
 *        @ORM\Index(name="fk_submission_section_submission1_idx", columns={"submission_id"}),
 *        @ORM\Index(name="fk_submission_section_comments_ref_data1_idx", columns={"submission_section"}),
 *        @ORM\Index(name="fk_submission_section_comments_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_submission_section_comments_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SubmissionSectionComment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CommentField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Submission section
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="submission_section", referencedColumnName="id", nullable=false)
     */
    protected $submissionSection;

    /**
     * Submission
     *
     * @var \Olcs\Db\Entity\Submission
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Submission", fetch="LAZY", inversedBy="submissionSectionComments")
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", nullable=false)
     */
    protected $submission;

    /**
     * Set the submission section
     *
     * @param \Olcs\Db\Entity\RefData $submissionSection
     * @return SubmissionSectionComment
     */
    public function setSubmissionSection($submissionSection)
    {
        $this->submissionSection = $submissionSection;

        return $this;
    }

    /**
     * Get the submission section
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getSubmissionSection()
    {
        return $this->submissionSection;
    }

    /**
     * Set the submission
     *
     * @param \Olcs\Db\Entity\Submission $submission
     * @return SubmissionSectionComment
     */
    public function setSubmission($submission)
    {
        $this->submission = $submission;

        return $this;
    }

    /**
     * Get the submission
     *
     * @return \Olcs\Db\Entity\Submission
     */
    public function getSubmission()
    {
        return $this->submission;
    }
}
