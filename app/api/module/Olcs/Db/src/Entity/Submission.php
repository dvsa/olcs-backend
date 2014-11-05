<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Submission Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission",
 *    indexes={
 *        @ORM\Index(name="fk_submission_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_submission_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_submission_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_submission_ref_data1_idx", columns={"submission_type"})
 *    }
 * )
 */
class Submission implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\ClosedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Submission type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="submission_type", referencedColumnName="id", nullable=false)
     */
    protected $submissionType;

    /**
     * Data snapshot
     *
     * @var string
     *
     * @ORM\Column(type="text", name="data_snapshot", length=65535, nullable=true)
     */
    protected $dataSnapshot;

    /**
     * Submission action
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\SubmissionAction", mappedBy="submission")
     */
    protected $submissionActions;

    /**
     * Submission section comment
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\SubmissionSectionComment", mappedBy="submission")
     */
    protected $submissionSectionComments;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->submissionActions = new ArrayCollection();
        $this->submissionSectionComments = new ArrayCollection();
    }

    /**
     * Set the submission type
     *
     * @param \Olcs\Db\Entity\RefData $submissionType
     * @return Submission
     */
    public function setSubmissionType($submissionType)
    {
        $this->submissionType = $submissionType;

        return $this;
    }

    /**
     * Get the submission type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getSubmissionType()
    {
        return $this->submissionType;
    }

    /**
     * Set the data snapshot
     *
     * @param string $dataSnapshot
     * @return Submission
     */
    public function setDataSnapshot($dataSnapshot)
    {
        $this->dataSnapshot = $dataSnapshot;

        return $this;
    }

    /**
     * Get the data snapshot
     *
     * @return string
     */
    public function getDataSnapshot()
    {
        return $this->dataSnapshot;
    }

    /**
     * Set the submission action
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionActions
     * @return Submission
     */
    public function setSubmissionActions($submissionActions)
    {
        $this->submissionActions = $submissionActions;

        return $this;
    }

    /**
     * Get the submission actions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubmissionActions()
    {
        return $this->submissionActions;
    }

    /**
     * Add a submission actions
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionActions
     * @return Submission
     */
    public function addSubmissionActions($submissionActions)
    {
        if ($submissionActions instanceof ArrayCollection) {
            $this->submissionActions = new ArrayCollection(
                array_merge(
                    $this->submissionActions->toArray(),
                    $submissionActions->toArray()
                )
            );
        } elseif (!$this->submissionActions->contains($submissionActions)) {
            $this->submissionActions->add($submissionActions);
        }

        return $this;
    }

    /**
     * Remove a submission actions
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionActions
     * @return Submission
     */
    public function removeSubmissionActions($submissionActions)
    {
        if ($this->submissionActions->contains($submissionActions)) {
            $this->submissionActions->removeElement($submissionActions);
        }

        return $this;
    }

    /**
     * Set the submission section comment
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionSectionComments
     * @return Submission
     */
    public function setSubmissionSectionComments($submissionSectionComments)
    {
        $this->submissionSectionComments = $submissionSectionComments;

        return $this;
    }

    /**
     * Get the submission section comments
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubmissionSectionComments()
    {
        return $this->submissionSectionComments;
    }

    /**
     * Add a submission section comments
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionSectionComments
     * @return Submission
     */
    public function addSubmissionSectionComments($submissionSectionComments)
    {
        if ($submissionSectionComments instanceof ArrayCollection) {
            $this->submissionSectionComments = new ArrayCollection(
                array_merge(
                    $this->submissionSectionComments->toArray(),
                    $submissionSectionComments->toArray()
                )
            );
        } elseif (!$this->submissionSectionComments->contains($submissionSectionComments)) {
            $this->submissionSectionComments->add($submissionSectionComments);
        }

        return $this;
    }

    /**
     * Remove a submission section comments
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionSectionComments
     * @return Submission
     */
    public function removeSubmissionSectionComments($submissionSectionComments)
    {
        if ($this->submissionSectionComments->contains($submissionSectionComments)) {
            $this->submissionSectionComments->removeElement($submissionSectionComments);
        }

        return $this;
    }
}
