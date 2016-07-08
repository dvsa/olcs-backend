<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ErruRequestFailure Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="erru_request_failure",
 *    indexes={
 *        @ORM\Index(name="ix_erru_request_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_erru_request_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_erru_request_failure_document_id", columns={"document_id"})
 *    }
 * )
 */
abstract class AbstractErruRequestFailure implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

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
     * Document
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\Document
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=true)
     */
    protected $document;

    /**
     * Errors
     *
     * @var string
     *
     * @ORM\Column(type="text", name="errors", length=65535, nullable=true)
     */
    protected $errors;

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
     * Input
     *
     * @var string
     *
     * @ORM\Column(type="text", name="input", length=65535, nullable=true)
     */
    protected $input;

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
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the created by
     *
     * @param int $createdBy
     * @return ErruRequestFailure
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
     * @param \DateTime $createdOn
     * @return ErruRequestFailure
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
     * Set the document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $document
     * @return ErruRequestFailure
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set the errors
     *
     * @param string $errors
     * @return ErruRequestFailure
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return ErruRequestFailure
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
     * Set the input
     *
     * @param string $input
     * @return ErruRequestFailure
     */
    public function setInput($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get the input
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy
     * @return ErruRequestFailure
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
     * @param \DateTime $lastModifiedOn
     * @return ErruRequestFailure
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
     * Set the version
     *
     * @param int $version
     * @return ErruRequestFailure
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
