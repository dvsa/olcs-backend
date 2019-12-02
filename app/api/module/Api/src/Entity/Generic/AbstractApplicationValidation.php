<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ApplicationValidation Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_validation",
 *    indexes={
 *        @ORM\Index(name="ix_application_validation_question_id", columns={"question_id"}),
 *        @ORM\Index(name="ix_application_validation_application_step_id",
     *     columns={"application_step_id"}),
 *        @ORM\Index(name="fk_application_validation_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_validation_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractApplicationValidation implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

    /**
     * Application step
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\ApplicationStep
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\ApplicationStep",
     *     fetch="LAZY",
     *     inversedBy="applicationValidations"
     * )
     * @ORM\JoinColumn(name="application_step_id", referencedColumnName="id", nullable=true)
     */
    protected $applicationStep;

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
     * Error translation key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="error_translation_key", length=255, nullable=true)
     */
    protected $errorTranslationKey;

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
     * @Gedmo\Blameable(on="update")
     */
    protected $lastModifiedBy;

    /**
     * Parameters
     *
     * @var string
     *
     * @ORM\Column(type="string", name="parameters", length=1024, nullable=true)
     */
    protected $parameters;

    /**
     * Question
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\Question
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\Question",
     *     fetch="LAZY",
     *     inversedBy="applicationValidations"
     * )
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=true)
     */
    protected $question;

    /**
     * Rule
     *
     * @var string
     *
     * @ORM\Column(type="string", name="rule", length=255, nullable=true)
     */
    protected $rule;

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
     * Set the application step
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\ApplicationStep $applicationStep entity being set as the value
     *
     * @return ApplicationValidation
     */
    public function setApplicationStep($applicationStep)
    {
        $this->applicationStep = $applicationStep;

        return $this;
    }

    /**
     * Get the application step
     *
     * @return \Dvsa\Olcs\Api\Entity\Generic\ApplicationStep
     */
    public function getApplicationStep()
    {
        return $this->applicationStep;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return ApplicationValidation
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
     * Set the error translation key
     *
     * @param string $errorTranslationKey new value being set
     *
     * @return ApplicationValidation
     */
    public function setErrorTranslationKey($errorTranslationKey)
    {
        $this->errorTranslationKey = $errorTranslationKey;

        return $this;
    }

    /**
     * Get the error translation key
     *
     * @return string
     */
    public function getErrorTranslationKey()
    {
        return $this->errorTranslationKey;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return ApplicationValidation
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return ApplicationValidation
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
     * Set the parameters
     *
     * @param string $parameters new value being set
     *
     * @return ApplicationValidation
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get the parameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set the question
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\Question $question entity being set as the value
     *
     * @return ApplicationValidation
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
     * Set the rule
     *
     * @param string $rule new value being set
     *
     * @return ApplicationValidation
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get the rule
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return ApplicationValidation
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
     * @return ApplicationValidation
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
}
