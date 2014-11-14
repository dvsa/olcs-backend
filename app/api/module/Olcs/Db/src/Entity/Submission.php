<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Submission Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission",
 *    indexes={
 *        @ORM\Index(name="fk_submission_case1_idx", 
 *            columns={"case_id"}),
 *        @ORM\Index(name="fk_submission_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_submission_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_submission_ref_data1_idx", 
 *            columns={"submission_type"})
 *    }
 * )
 */
class Submission implements Interfaces\EntityInterface
{

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
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Closed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="closed_date", nullable=true)
     */
    protected $closedDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the closed date
     *
     * @param \DateTime $closedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setClosedDate($closedDate)
    {
        $this->closedDate = $closedDate;

        return $this;
    }

    /**
     * Get the closed date
     *
     * @return \DateTime
     */
    public function getClosedDate()
    {
        return $this->closedDate;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
