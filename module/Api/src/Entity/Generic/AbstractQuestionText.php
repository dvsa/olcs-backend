<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * QuestionText Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="question_text",
 *    indexes={
 *        @ORM\Index(name="ix_question_text_question_id", columns={"question_id"}),
 *        @ORM\Index(name="fk_question_text_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_question_text_last_modified_by_user_id", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractQuestionText implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Additional guidance
     *
     * @var string
     *
     * @ORM\Column(type="string", name="additional_guidance", length=255, nullable=true)
     */
    protected $additionalGuidance;

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
     * Details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="details", length=255, nullable=true)
     */
    protected $details;

    /**
     * Effective from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="effective_from", nullable=true)
     */
    protected $effectiveFrom;

    /**
     * Error label
     *
     * @var string
     *
     * @ORM\Column(type="string", name="error_label", length=255, nullable=true)
     */
    protected $errorLabel;

    /**
     * Guidance
     *
     * @var string
     *
     * @ORM\Column(type="string", name="guidance", length=255, nullable=true)
     */
    protected $guidance;

    /**
     * Heading
     *
     * @var string
     *
     * @ORM\Column(type="string", name="heading", length=255, nullable=true)
     */
    protected $heading;

    /**
     * Heading caption
     *
     * @var string
     *
     * @ORM\Column(type="string", name="heading_caption", length=255, nullable=true)
     */
    protected $headingCaption;

    /**
     * Hint
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hint", length=255, nullable=true)
     */
    protected $hint;

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
     * Label
     *
     * @var string
     *
     * @ORM\Column(type="string", name="label", length=255, nullable=true)
     */
    protected $label;

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
     * Question
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\Question
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\Question",
     *     fetch="LAZY",
     *     inversedBy="questionTexts"
     * )
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
     * Warning
     *
     * @var string
     *
     * @ORM\Column(type="string", name="warning", length=255, nullable=true)
     */
    protected $warning;

    /**
     * Set the additional guidance
     *
     * @param string $additionalGuidance new value being set
     *
     * @return QuestionText
     */
    public function setAdditionalGuidance($additionalGuidance)
    {
        $this->additionalGuidance = $additionalGuidance;

        return $this;
    }

    /**
     * Get the additional guidance
     *
     * @return string
     */
    public function getAdditionalGuidance()
    {
        return $this->additionalGuidance;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return QuestionText
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
     * @return QuestionText
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
     * Set the details
     *
     * @param string $details new value being set
     *
     * @return QuestionText
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the effective from
     *
     * @param \DateTime $effectiveFrom new value being set
     *
     * @return QuestionText
     */
    public function setEffectiveFrom($effectiveFrom)
    {
        $this->effectiveFrom = $effectiveFrom;

        return $this;
    }

    /**
     * Get the effective from
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getEffectiveFrom($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->effectiveFrom);
        }

        return $this->effectiveFrom;
    }

    /**
     * Set the error label
     *
     * @param string $errorLabel new value being set
     *
     * @return QuestionText
     */
    public function setErrorLabel($errorLabel)
    {
        $this->errorLabel = $errorLabel;

        return $this;
    }

    /**
     * Get the error label
     *
     * @return string
     */
    public function getErrorLabel()
    {
        return $this->errorLabel;
    }

    /**
     * Set the guidance
     *
     * @param string $guidance new value being set
     *
     * @return QuestionText
     */
    public function setGuidance($guidance)
    {
        $this->guidance = $guidance;

        return $this;
    }

    /**
     * Get the guidance
     *
     * @return string
     */
    public function getGuidance()
    {
        return $this->guidance;
    }

    /**
     * Set the heading
     *
     * @param string $heading new value being set
     *
     * @return QuestionText
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get the heading
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set the heading caption
     *
     * @param string $headingCaption new value being set
     *
     * @return QuestionText
     */
    public function setHeadingCaption($headingCaption)
    {
        $this->headingCaption = $headingCaption;

        return $this;
    }

    /**
     * Get the heading caption
     *
     * @return string
     */
    public function getHeadingCaption()
    {
        return $this->headingCaption;
    }

    /**
     * Set the hint
     *
     * @param string $hint new value being set
     *
     * @return QuestionText
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * Get the hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return QuestionText
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
     * Set the label
     *
     * @param string $label new value being set
     *
     * @return QuestionText
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return QuestionText
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
     * @return QuestionText
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
     * Set the question
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\Question $question entity being set as the value
     *
     * @return QuestionText
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
     * @return QuestionText
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
     * Set the warning
     *
     * @param string $warning new value being set
     *
     * @return QuestionText
     */
    public function setWarning($warning)
    {
        $this->warning = $warning;

        return $this;
    }

    /**
     * Get the warning
     *
     * @return string
     */
    public function getWarning()
    {
        return $this->warning;
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
                $this->$property = null;
            }
        }
    }
}
