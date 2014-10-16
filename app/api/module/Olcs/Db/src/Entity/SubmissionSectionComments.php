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
 *        @ORM\Index(name="fk_submission_section_submission1_idx", columns={"submission_id"})
 *    }
 * )
 */
class SubmissionSectionComments implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Submission
     *
     * @var \Olcs\Db\Entity\Submission
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Submission", fetch="LAZY")
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", nullable=false)
     */
    protected $submission;

    /**
     * Comments
     *
     * @var string
     *
     * @ORM\Column(type="text", name="comments", length=65535, nullable=true)
     */
    protected $comments;

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
     * Set the comments
     *
     * @param string $comments
     * @return SubmissionSectionComments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get the comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }
}
