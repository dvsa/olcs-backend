<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ApplicationStep Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_step",
 *    indexes={
 *        @ORM\Index(name="ix_application_step_application_path_id", columns={"application_path_id"}),
 *        @ORM\Index(name="ix_application_step_question_id", columns={"question_id"}),
 *        @ORM\Index(name="ix_application_step_parent_id", columns={"parent_id"}),
 *        @ORM\Index(name="fk_application_step_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_step_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractApplicationStep implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;

    /**
     * Application path
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\ApplicationPath
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\ApplicationPath",
     *     fetch="LAZY",
     *     inversedBy="applicationSteps"
     * )
     * @ORM\JoinColumn(name="application_path_id", referencedColumnName="id", nullable=false)
     */
    protected $applicationPath;

    /**
     * Break on failure
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="break_on_failure", nullable=true)
     */
    protected $breakOnFailure;

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
     * Ignore question validation
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="ignore_question_validation", nullable=true)
     */
    protected $ignoreQuestionValidation;

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
     * Mandatory
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="mandatory", nullable=true)
     */
    protected $mandatory;

    /**
     * Multiple responses
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="multiple_responses", nullable=true)
     */
    protected $multipleResponses;

    /**
     * Only on yes
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="only_on_yes", nullable=true)
     */
    protected $onlyOnYes;

    /**
     * Option filter field
     *
     * @var string
     *
     * @ORM\Column(type="string", name="option_filter_field", length=255, nullable=true)
     */
    protected $optionFilterField;

    /**
     * Option filter value
     *
     * @var string
     *
     * @ORM\Column(type="string", name="option_filter_value", length=255, nullable=true)
     */
    protected $optionFilterValue;

    /**
     * Parent
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\ApplicationStep
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Generic\ApplicationStep", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Question
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\Question
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Generic\Question", fetch="LAZY")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    protected $question;

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
     * Weight
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="weight", precision=10, scale=2, nullable=true)
     */
    protected $weight;

    /**
     * Application validation
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\ApplicationValidation",
     *     mappedBy="applicationStep"
     * )
     */
    protected $applicationValidations;

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
        $this->applicationValidations = new ArrayCollection();
    }

    /**
     * Set the application path
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\ApplicationPath $applicationPath entity being set as the value
     *
     * @return ApplicationStep
     */
    public function setApplicationPath($applicationPath)
    {
        $this->applicationPath = $applicationPath;

        return $this;
    }

    /**
     * Get the application path
     *
     * @return \Dvsa\Olcs\Api\Entity\Generic\ApplicationPath
     */
    public function getApplicationPath()
    {
        return $this->applicationPath;
    }

    /**
     * Set the break on failure
     *
     * @param boolean $breakOnFailure new value being set
     *
     * @return ApplicationStep
     */
    public function setBreakOnFailure($breakOnFailure)
    {
        $this->breakOnFailure = $breakOnFailure;

        return $this;
    }

    /**
     * Get the break on failure
     *
     * @return boolean
     */
    public function getBreakOnFailure()
    {
        return $this->breakOnFailure;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return ApplicationStep
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
     * @return ApplicationStep
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
     * @return ApplicationStep
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
     * Set the ignore question validation
     *
     * @param boolean $ignoreQuestionValidation new value being set
     *
     * @return ApplicationStep
     */
    public function setIgnoreQuestionValidation($ignoreQuestionValidation)
    {
        $this->ignoreQuestionValidation = $ignoreQuestionValidation;

        return $this;
    }

    /**
     * Get the ignore question validation
     *
     * @return boolean
     */
    public function getIgnoreQuestionValidation()
    {
        return $this->ignoreQuestionValidation;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return ApplicationStep
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
     * @return ApplicationStep
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
     * Set the mandatory
     *
     * @param boolean $mandatory new value being set
     *
     * @return ApplicationStep
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get the mandatory
     *
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set the multiple responses
     *
     * @param boolean $multipleResponses new value being set
     *
     * @return ApplicationStep
     */
    public function setMultipleResponses($multipleResponses)
    {
        $this->multipleResponses = $multipleResponses;

        return $this;
    }

    /**
     * Get the multiple responses
     *
     * @return boolean
     */
    public function getMultipleResponses()
    {
        return $this->multipleResponses;
    }

    /**
     * Set the only on yes
     *
     * @param boolean $onlyOnYes new value being set
     *
     * @return ApplicationStep
     */
    public function setOnlyOnYes($onlyOnYes)
    {
        $this->onlyOnYes = $onlyOnYes;

        return $this;
    }

    /**
     * Get the only on yes
     *
     * @return boolean
     */
    public function getOnlyOnYes()
    {
        return $this->onlyOnYes;
    }

    /**
     * Set the option filter field
     *
     * @param string $optionFilterField new value being set
     *
     * @return ApplicationStep
     */
    public function setOptionFilterField($optionFilterField)
    {
        $this->optionFilterField = $optionFilterField;

        return $this;
    }

    /**
     * Get the option filter field
     *
     * @return string
     */
    public function getOptionFilterField()
    {
        return $this->optionFilterField;
    }

    /**
     * Set the option filter value
     *
     * @param string $optionFilterValue new value being set
     *
     * @return ApplicationStep
     */
    public function setOptionFilterValue($optionFilterValue)
    {
        $this->optionFilterValue = $optionFilterValue;

        return $this;
    }

    /**
     * Get the option filter value
     *
     * @return string
     */
    public function getOptionFilterValue()
    {
        return $this->optionFilterValue;
    }

    /**
     * Set the parent
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\ApplicationStep $parent entity being set as the value
     *
     * @return ApplicationStep
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent
     *
     * @return \Dvsa\Olcs\Api\Entity\Generic\ApplicationStep
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the question
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\Question $question entity being set as the value
     *
     * @return ApplicationStep
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get the question
     *
     * @return \Dvsa\Olcs\Api\Entity\Generic\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return ApplicationStep
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
     * Set the weight
     *
     * @param float $weight new value being set
     *
     * @return ApplicationStep
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get the weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set the application validation
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationValidations collection being set as the value
     *
     * @return ApplicationStep
     */
    public function setApplicationValidations($applicationValidations)
    {
        $this->applicationValidations = $applicationValidations;

        return $this;
    }

    /**
     * Get the application validations
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplicationValidations()
    {
        return $this->applicationValidations;
    }

    /**
     * Add a application validations
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationValidations collection being added
     *
     * @return ApplicationStep
     */
    public function addApplicationValidations($applicationValidations)
    {
        if ($applicationValidations instanceof ArrayCollection) {
            $this->applicationValidations = new ArrayCollection(
                array_merge(
                    $this->applicationValidations->toArray(),
                    $applicationValidations->toArray()
                )
            );
        } elseif (!$this->applicationValidations->contains($applicationValidations)) {
            $this->applicationValidations->add($applicationValidations);
        }

        return $this;
    }

    /**
     * Remove a application validations
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationValidations collection being removed
     *
     * @return ApplicationStep
     */
    public function removeApplicationValidations($applicationValidations)
    {
        if ($this->applicationValidations->contains($applicationValidations)) {
            $this->applicationValidations->removeElement($applicationValidations);
        }

        return $this;
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
}
