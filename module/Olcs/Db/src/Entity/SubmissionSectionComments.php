<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SubmissionSectionComments Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission_section_comments",
 *    indexes={
 *        @ORM\Index(name="fk_submission_section_submission1_idx", columns={"submission_id"}),
 *        @ORM\Index(name="fk_submission_section_comments_ref_data1_idx", columns={"submission_section"}),
 *        @ORM\Index(name="fk_submission_section_comments_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_submission_section_comments_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SubmissionSectionComments implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Submission section
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="submission_section", referencedColumnName="id", nullable=false)
     */
    protected $submissionSection;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="text", name="comment", length=65535, nullable=true)
     */
    protected $comment;

    /**
     * Set the submission
     *
     * @param \Olcs\Db\Entity\Submission $submission
     * @return SubmissionSectionComments
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

    /**
     * Set the submission section
     *
     * @param \Olcs\Db\Entity\RefData $submissionSection
     * @return SubmissionSectionComments
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
     * Set the comment
     *
     * @param string $comment
     * @return SubmissionSectionComments
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
