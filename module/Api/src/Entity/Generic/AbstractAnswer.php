<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Answer Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="answer",
 *    indexes={
 *        @ORM\Index(name="ix_answer_question_text_id", columns={"question_text_id"}),
 *        @ORM\Index(name="fk_answer_irhp_permit_application_id_irhp_permit_application_id",
     *     columns={"irhp_permit_application_id"}),
 *        @ORM\Index(name="fk_answer_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_answer_last_modified_by_user_id", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractAnswer implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Array
     *
     * @var string
     *
     * @ORM\Column(type="text", name="array", nullable=true)
     */
    protected $array;

    /**
     * Boolean
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="boolean", nullable=true)
     */
    protected $boolean;

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
     * Date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date", nullable=true)
     */
    protected $date;

    /**
     * Datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="datetime", nullable=true)
     */
    protected $datetime;

    /**
     * Decimal
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="decimal", precision=18, scale=4, nullable=true)
     */
    protected $decimal;

    /**
     * Document id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="document_id", nullable=true)
     */
    protected $documentId;

    /**
     * Filename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="filename", length=255, nullable=true)
     */
    protected $filename;

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
     * Integer
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="integer", nullable=true)
     */
    protected $integer;

    /**
     * Irhp permit application
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication",
     *     fetch="LAZY",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="irhp_permit_application_id", referencedColumnName="id", nullable=true)
     */
    protected $irhpPermitApplication;

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
     * Question text
     *
     * @var \Dvsa\Olcs\Api\Entity\Generic\QuestionText
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Generic\QuestionText", fetch="LAZY")
     * @ORM\JoinColumn(name="question_text_id", referencedColumnName="id", nullable=false)
     */
    protected $questionText;

    /**
     * String
     *
     * @var string
     *
     * @ORM\Column(type="string", name="string", length=255, nullable=true)
     */
    protected $string;

    /**
     * Text
     *
     * @var string
     *
     * @ORM\Column(type="text", name="text", nullable=true)
     */
    protected $text;

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
     * Set the array
     *
     * @param string $array new value being set
     *
     * @return Answer
     */
    public function setArray($array)
    {
        $this->array = $array;

        return $this;
    }

    /**
     * Get the array
     *
     * @return string
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Set the boolean
     *
     * @param boolean $boolean new value being set
     *
     * @return Answer
     */
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;

        return $this;
    }

    /**
     * Get the boolean
     *
     * @return boolean
     */
    public function getBoolean()
    {
        return $this->boolean;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return Answer
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
     * @return Answer
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
     * Set the date
     *
     * @param \DateTime $date new value being set
     *
     * @return Answer
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->date);
        }

        return $this->date;
    }

    /**
     * Set the datetime
     *
     * @param \DateTime $datetime new value being set
     *
     * @return Answer
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get the datetime
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDatetime($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->datetime);
        }

        return $this->datetime;
    }

    /**
     * Set the decimal
     *
     * @param float $decimal new value being set
     *
     * @return Answer
     */
    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;

        return $this;
    }

    /**
     * Get the decimal
     *
     * @return float
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * Set the document id
     *
     * @param int $documentId new value being set
     *
     * @return Answer
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }

    /**
     * Get the document id
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set the filename
     *
     * @param string $filename new value being set
     *
     * @return Answer
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return Answer
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
     * Set the integer
     *
     * @param int $integer new value being set
     *
     * @return Answer
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;

        return $this;
    }

    /**
     * Get the integer
     *
     * @return int
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication $irhpPermitApplication entity being set as the value
     *
     * @return Answer
     */
    public function setIrhpPermitApplication($irhpPermitApplication)
    {
        $this->irhpPermitApplication = $irhpPermitApplication;

        return $this;
    }

    /**
     * Get the irhp permit application
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication
     */
    public function getIrhpPermitApplication()
    {
        return $this->irhpPermitApplication;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return Answer
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
     * @return Answer
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
     * Set the question text
     *
     * @param \Dvsa\Olcs\Api\Entity\Generic\QuestionText $questionText entity being set as the value
     *
     * @return Answer
     */
    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;

        return $this;
    }

    /**
     * Get the question text
     *
     * @return \Dvsa\Olcs\Api\Entity\Generic\QuestionText
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }

    /**
     * Set the string
     *
     * @param string $string new value being set
     *
     * @return Answer
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * Get the string
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set the text
     *
     * @param string $text new value being set
     *
     * @return Answer
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Answer
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
                $this->$property = null;
            }
        }
    }
}
