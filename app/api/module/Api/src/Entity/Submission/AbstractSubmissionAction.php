<?php

namespace Dvsa\Olcs\Api\Entity\Submission;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SubmissionAction Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission_action",
 *    indexes={
 *        @ORM\Index(name="ix_submission_action_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_submission_action_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_submission_action_submission_id", columns={"submission_id"})
 *    }
 * )
 */
abstract class AbstractSubmissionAction implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Action type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\RefData",
     *     inversedBy="submissionActions",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="submission_action_type",
     *     joinColumns={
     *         @ORM\JoinColumn(name="submission_action_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="action_type", referencedColumnName="id")
     *     }
     * )
     */
    protected $actionTypes;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="text", name="comment", length=16777215, nullable=true)
     */
    protected $comment;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
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
     * Is decision
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_decision", nullable=false)
     */
    protected $isDecision;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="update")
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
     * Reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Pi\Reason",
     *     inversedBy="submissionActions",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="submission_action_reason",
     *     joinColumns={
     *         @ORM\JoinColumn(name="submission_action_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="reason_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $reasons;

    /**
     * Submission
     *
     * @var \Dvsa\Olcs\Api\Entity\Submission\Submission
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Submission\Submission",
     *     fetch="LAZY",
     *     inversedBy="submissionActions"
     * )
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", nullable=false)
     */
    protected $submission;

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
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->reasons = new ArrayCollection();
        $this->actionTypes = new ArrayCollection();
    }

    /**
     * Set the action type
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $actionTypes collection being set as the value
     *
     * @return SubmissionAction
     */
    public function setActionTypes($actionTypes)
    {
        $this->actionTypes = $actionTypes;

        return $this;
    }

    /**
     * Get the action types
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getActionTypes()
    {
        return $this->actionTypes;
    }

    /**
     * Add a action types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $actionTypes collection being added
     *
     * @return SubmissionAction
     */
    public function addActionTypes($actionTypes)
    {
        if ($actionTypes instanceof ArrayCollection) {
            $this->actionTypes = new ArrayCollection(
                array_merge(
                    $this->actionTypes->toArray(),
                    $actionTypes->toArray()
                )
            );
        } elseif (!$this->actionTypes->contains($actionTypes)) {
            $this->actionTypes->add($actionTypes);
        }

        return $this;
    }

    /**
     * Remove a action types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $actionTypes collection being removed
     *
     * @return SubmissionAction
     */
    public function removeActionTypes($actionTypes)
    {
        if ($this->actionTypes->contains($actionTypes)) {
            $this->actionTypes->removeElement($actionTypes);
        }

        return $this;
    }

    /**
     * Set the comment
     *
     * @param string $comment new value being set
     *
     * @return SubmissionAction
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

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return SubmissionAction
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
     * @param \DateTime $createdOn new value being set
     *
     * @return SubmissionAction
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCreatedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->createdOn);
        }

        return $this->createdOn;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return SubmissionAction
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
     * Set the is decision
     *
     * @param string $isDecision new value being set
     *
     * @return SubmissionAction
     */
    public function setIsDecision($isDecision)
    {
        $this->isDecision = $isDecision;

        return $this;
    }

    /**
     * Get the is decision
     *
     * @return string
     */
    public function getIsDecision()
    {
        return $this->isDecision;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return SubmissionAction
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
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return SubmissionAction
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastModifiedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastModifiedOn);
        }

        return $this->lastModifiedOn;
    }

    /**
     * Set the reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons collection being set as the value
     *
     * @return SubmissionAction
     */
    public function setReasons($reasons)
    {
        $this->reasons = $reasons;

        return $this;
    }

    /**
     * Get the reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * Add a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons collection being added
     *
     * @return SubmissionAction
     */
    public function addReasons($reasons)
    {
        if ($reasons instanceof ArrayCollection) {
            $this->reasons = new ArrayCollection(
                array_merge(
                    $this->reasons->toArray(),
                    $reasons->toArray()
                )
            );
        } elseif (!$this->reasons->contains($reasons)) {
            $this->reasons->add($reasons);
        }

        return $this;
    }

    /**
     * Remove a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons collection being removed
     *
     * @return SubmissionAction
     */
    public function removeReasons($reasons)
    {
        if ($this->reasons->contains($reasons)) {
            $this->reasons->removeElement($reasons);
        }

        return $this;
    }

    /**
     * Set the submission
     *
     * @param \Dvsa\Olcs\Api\Entity\Submission\Submission $submission entity being set as the value
     *
     * @return SubmissionAction
     */
    public function setSubmission($submission)
    {
        $this->submission = $submission;

        return $this;
    }

    /**
     * Get the submission
     *
     * @return \Dvsa\Olcs\Api\Entity\Submission\Submission
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return SubmissionAction
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
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
