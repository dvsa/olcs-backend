<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
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
    use ClearPropertiesTrait;

    /**
     * Additional guidance key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="additional_guidance_key", length=255, nullable=true)
     */
    protected $additionalGuidanceKey;

    /**
     * Bullet list key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="bullet_list_key", length=255, nullable=true)
     */
    protected $bulletListKey;

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
     * Details key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="details_key", length=255, nullable=true)
     */
    protected $detailsKey;

    /**
     * Effective from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="effective_from", nullable=true)
     */
    protected $effectiveFrom;

    /**
     * Guidance key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="guidance_key", length=255, nullable=true)
     */
    protected $guidanceKey;

    /**
     * Hint key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hint_key", length=255, nullable=true)
     */
    protected $hintKey;

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
     * Label key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="label_key", length=255, nullable=true)
     */
    protected $labelKey;

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
     * Question key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="question_key", length=255, nullable=true)
     */
    protected $questionKey;

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
     * Warning key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="warning_key", length=255, nullable=true)
     */
    protected $warningKey;

    /**
     * Set the additional guidance key
     *
     * @param string $additionalGuidanceKey new value being set
     *
     * @return QuestionText
     */
    public function setAdditionalGuidanceKey($additionalGuidanceKey)
    {
        $this->additionalGuidanceKey = $additionalGuidanceKey;

        return $this;
    }

    /**
     * Get the additional guidance key
     *
     * @return string
     */
    public function getAdditionalGuidanceKey()
    {
        return $this->additionalGuidanceKey;
    }

    /**
     * Set the bullet list key
     *
     * @param string $bulletListKey new value being set
     *
     * @return QuestionText
     */
    public function setBulletListKey($bulletListKey)
    {
        $this->bulletListKey = $bulletListKey;

        return $this;
    }

    /**
     * Get the bullet list key
     *
     * @return string
     */
    public function getBulletListKey()
    {
        return $this->bulletListKey;
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
     * Set the details key
     *
     * @param string $detailsKey new value being set
     *
     * @return QuestionText
     */
    public function setDetailsKey($detailsKey)
    {
        $this->detailsKey = $detailsKey;

        return $this;
    }

    /**
     * Get the details key
     *
     * @return string
     */
    public function getDetailsKey()
    {
        return $this->detailsKey;
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
     * Set the guidance key
     *
     * @param string $guidanceKey new value being set
     *
     * @return QuestionText
     */
    public function setGuidanceKey($guidanceKey)
    {
        $this->guidanceKey = $guidanceKey;

        return $this;
    }

    /**
     * Get the guidance key
     *
     * @return string
     */
    public function getGuidanceKey()
    {
        return $this->guidanceKey;
    }

    /**
     * Set the hint key
     *
     * @param string $hintKey new value being set
     *
     * @return QuestionText
     */
    public function setHintKey($hintKey)
    {
        $this->hintKey = $hintKey;

        return $this;
    }

    /**
     * Get the hint key
     *
     * @return string
     */
    public function getHintKey()
    {
        return $this->hintKey;
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
     * Set the label key
     *
     * @param string $labelKey new value being set
     *
     * @return QuestionText
     */
    public function setLabelKey($labelKey)
    {
        $this->labelKey = $labelKey;

        return $this;
    }

    /**
     * Get the label key
     *
     * @return string
     */
    public function getLabelKey()
    {
        return $this->labelKey;
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
     * Set the question key
     *
     * @param string $questionKey new value being set
     *
     * @return QuestionText
     */
    public function setQuestionKey($questionKey)
    {
        $this->questionKey = $questionKey;

        return $this;
    }

    /**
     * Get the question key
     *
     * @return string
     */
    public function getQuestionKey()
    {
        return $this->questionKey;
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
     * Set the warning key
     *
     * @param string $warningKey new value being set
     *
     * @return QuestionText
     */
    public function setWarningKey($warningKey)
    {
        $this->warningKey = $warningKey;

        return $this;
    }

    /**
     * Get the warning key
     *
     * @return string
     */
    public function getWarningKey()
    {
        return $this->warningKey;
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
