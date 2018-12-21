<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpApplication Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_application",
 *    indexes={
 *        @ORM\Index(name="ix_irhp_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_irhp_application_source", columns={"source"}),
 *        @ORM\Index(name="ix_irhp_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_irhp_application_irhp_permit_type_id", columns={"irhp_permit_type_id"}),
 *        @ORM\Index(name="ix_irhp_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irhp_application_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractIrhpApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Checked answers
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="checked_answers", nullable=false, options={"default": 0})
     */
    protected $checkedAnswers = 0;

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
     * Date received
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_received", nullable=false)
     */
    protected $dateReceived;

    /**
     * Declaration
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="declaration", nullable=false, options={"default": 0})
     */
    protected $declaration = 0;

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
     * Irhp permit type
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitType;

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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Source
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="source", referencedColumnName="id", nullable=false)
     */
    protected $source;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    protected $status;

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
     * Set the checked answers
     *
     * @param boolean $checkedAnswers new value being set
     *
     * @return IrhpApplication
     */
    public function setCheckedAnswers($checkedAnswers)
    {
        $this->checkedAnswers = $checkedAnswers;

        return $this;
    }

    /**
     * Get the checked answers
     *
     * @return boolean
     */
    public function getCheckedAnswers()
    {
        return $this->checkedAnswers;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpApplication
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
     * @return IrhpApplication
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
     * Set the date received
     *
     * @param \DateTime $dateReceived new value being set
     *
     * @return IrhpApplication
     */
    public function setDateReceived($dateReceived)
    {
        $this->dateReceived = $dateReceived;

        return $this;
    }

    /**
     * Get the date received
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDateReceived($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->dateReceived);
        }

        return $this->dateReceived;
    }

    /**
     * Set the declaration
     *
     * @param boolean $declaration new value being set
     *
     * @return IrhpApplication
     */
    public function setDeclaration($declaration)
    {
        $this->declaration = $declaration;

        return $this;
    }

    /**
     * Get the declaration
     *
     * @return boolean
     */
    public function getDeclaration()
    {
        return $this->declaration;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpApplication
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
     * Set the irhp permit type
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType $irhpPermitType entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setIrhpPermitType($irhpPermitType)
    {
        $this->irhpPermitType = $irhpPermitType;

        return $this;
    }

    /**
     * Get the irhp permit type
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType
     */
    public function getIrhpPermitType()
    {
        return $this->irhpPermitType;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpApplication
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
     * @return IrhpApplication
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
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the source
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $source entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the source
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpApplication
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
