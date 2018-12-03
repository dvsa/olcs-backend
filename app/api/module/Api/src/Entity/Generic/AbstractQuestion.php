<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Question Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="question",
 *    indexes={
 *        @ORM\Index(name="fk_question_question_type_ref_data_id", columns={"question_type"})
 *    }
 * )
 */
abstract class AbstractQuestion implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Created by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="created_by", nullable=true)
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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

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
     * @var int
     *
     * @ORM\Column(type="integer", name="last_modified_by", nullable=true)
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
     * Option source
     *
     * @var string
     *
     * @ORM\Column(type="string", name="option_source", length=255, nullable=true)
     */
    protected $optionSource;

    /**
     * Question type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="question_type", referencedColumnName="id", nullable=true)
     */
    protected $questionType;

    /**
     * Title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="title", length=100, nullable=true)
     */
    protected $title;

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
     * Application validation
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\ApplicationValidation",
     *     mappedBy="question"
     * )
     */
    protected $applicationValidations;

    /**
     * Question text
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Generic\QuestionText", mappedBy="question")
     */
    protected $questionTexts;

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
        $this->questionTexts = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param int $createdBy new value being set
     *
     * @return Question
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return int
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
     * @return Question
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
     * Set the description
     *
     * @param string $description new value being set
     *
     * @return Question
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return Question
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
     * @param int $lastModifiedBy new value being set
     *
     * @return Question
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return int
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
     * @return Question
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
     * Set the option source
     *
     * @param string $optionSource new value being set
     *
     * @return Question
     */
    public function setOptionSource($optionSource)
    {
        $this->optionSource = $optionSource;

        return $this;
    }

    /**
     * Get the option source
     *
     * @return string
     */
    public function getOptionSource()
    {
        return $this->optionSource;
    }

    /**
     * Set the question type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $questionType entity being set as the value
     *
     * @return Question
     */
    public function setQuestionType($questionType)
    {
        $this->questionType = $questionType;

        return $this;
    }

    /**
     * Get the question type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * Set the title
     *
     * @param string $title new value being set
     *
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Question
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
     * Set the application validation
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationValidations collection being set as the value
     *
     * @return Question
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
     * @return Question
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
     * @return Question
     */
    public function removeApplicationValidations($applicationValidations)
    {
        if ($this->applicationValidations->contains($applicationValidations)) {
            $this->applicationValidations->removeElement($applicationValidations);
        }

        return $this;
    }

    /**
     * Set the question text
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $questionTexts collection being set as the value
     *
     * @return Question
     */
    public function setQuestionTexts($questionTexts)
    {
        $this->questionTexts = $questionTexts;

        return $this;
    }

    /**
     * Get the question texts
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getQuestionTexts()
    {
        return $this->questionTexts;
    }

    /**
     * Add a question texts
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $questionTexts collection being added
     *
     * @return Question
     */
    public function addQuestionTexts($questionTexts)
    {
        if ($questionTexts instanceof ArrayCollection) {
            $this->questionTexts = new ArrayCollection(
                array_merge(
                    $this->questionTexts->toArray(),
                    $questionTexts->toArray()
                )
            );
        } elseif (!$this->questionTexts->contains($questionTexts)) {
            $this->questionTexts->add($questionTexts);
        }

        return $this;
    }

    /**
     * Remove a question texts
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $questionTexts collection being removed
     *
     * @return Question
     */
    public function removeQuestionTexts($questionTexts)
    {
        if ($this->questionTexts->contains($questionTexts)) {
            $this->questionTexts->removeElement($questionTexts);
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
