<?php

namespace Dvsa\Olcs\Api\Entity\Submission;

use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\JsonSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Submission Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="submission",
 *    indexes={
 *        @ORM\Index(name="ix_submission_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_submission_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_submission_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_submission_submission_type", columns={"submission_type"}),
 *        @ORM\Index(name="fk_submission_user1_idx", columns={"sender_user_id"}),
 *        @ORM\Index(name="fk_submission_user2_idx", columns={"recipient_user_id"})
 *    }
 * )
 */
abstract class AbstractSubmission implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases", fetch="LAZY")
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
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Data snapshot
     *
     * @var string
     *
     * @ORM\Column(type="text", name="data_snapshot", length=65535, nullable=true)
     */
    protected $dataSnapshot;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

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
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Recipient user
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="recipient_user_id", referencedColumnName="id", nullable=true)
     */
    protected $recipientUser;

    /**
     * Sender user
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", nullable=true)
     */
    protected $senderUser;

    /**
     * Submission type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="submission_type", referencedColumnName="id", nullable=false)
     */
    protected $submissionType;

    /**
     * Urgent
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="urgent", nullable=true)
     */
    protected $urgent;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document", mappedBy="submission")
     */
    protected $documents;

    /**
     * Submission action
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Submission\SubmissionAction",
     *     mappedBy="submission"
     * )
     */
    protected $submissionActions;

    /**
     * Submission section comment
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Submission\SubmissionSectionComment",
     *     mappedBy="submission"
     * )
     */
    protected $submissionSectionComments;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->documents = new ArrayCollection();
        $this->submissionActions = new ArrayCollection();
        $this->submissionSectionComments = new ArrayCollection();
    }

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case
     * @return Submission
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the closed date
     *
     * @param \DateTime $closedDate
     * @return Submission
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Submission
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return Submission
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
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return Submission
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Submission
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Submission
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return Submission
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
     * Set the recipient user
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $recipientUser
     * @return Submission
     */
    public function setRecipientUser($recipientUser)
    {
        $this->recipientUser = $recipientUser;

        return $this;
    }

    /**
     * Get the recipient user
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getRecipientUser()
    {
        return $this->recipientUser;
    }

    /**
     * Set the sender user
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $senderUser
     * @return Submission
     */
    public function setSenderUser($senderUser)
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    /**
     * Get the sender user
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getSenderUser()
    {
        return $this->senderUser;
    }

    /**
     * Set the submission type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $submissionType
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getSubmissionType()
    {
        return $this->submissionType;
    }

    /**
     * Set the urgent
     *
     * @param string $urgent
     * @return Submission
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * Get the urgent
     *
     * @return string
     */
    public function getUrgent()
    {
        return $this->urgent;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return Submission
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
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Submission
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get the documents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Submission
     */
    public function addDocuments($documents)
    {
        if ($documents instanceof ArrayCollection) {
            $this->documents = new ArrayCollection(
                array_merge(
                    $this->documents->toArray(),
                    $documents->toArray()
                )
            );
        } elseif (!$this->documents->contains($documents)) {
            $this->documents->add($documents);
        }

        return $this;
    }

    /**
     * Remove a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Submission
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
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
}
