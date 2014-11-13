<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmQualification Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_qualification",
 *    indexes={
 *        @ORM\Index(name="fk_qualification_transport_manager1_idx", 
 *            columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_qualification_country1_idx", 
 *            columns={"country_code"}),
 *        @ORM\Index(name="fk_qualification_ref_data1_idx", 
 *            columns={"qualification_type"}),
 *        @ORM\Index(name="fk_tm_qualification_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_qualification_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class TmQualification implements Interfaces\EntityInterface
{

    /**
     * Qualification type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="qualification_type", referencedColumnName="id", nullable=false)
     */
    protected $qualificationType;

    /**
     * Country code
     *
     * @var \Olcs\Db\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Country", fetch="LAZY")
     * @ORM\JoinColumn(name="country_code", referencedColumnName="id", nullable=false)
     */
    protected $countryCode;

    /**
     * Issued date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="issued_date", nullable=true)
     */
    protected $issuedDate;

    /**
     * Serial no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="serial_no", length=20, nullable=true)
     */
    protected $serialNo;

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
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", fetch="LAZY")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManager;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

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
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

    /**
     * Set the qualification type
     *
     * @param \Olcs\Db\Entity\RefData $qualificationType
     * @return TmQualification
     */
    public function setQualificationType($qualificationType)
    {
        $this->qualificationType = $qualificationType;

        return $this;
    }

    /**
     * Get the qualification type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getQualificationType()
    {
        return $this->qualificationType;
    }

    /**
     * Set the country code
     *
     * @param \Olcs\Db\Entity\Country $countryCode
     * @return TmQualification
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get the country code
     *
     * @return \Olcs\Db\Entity\Country
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the issued date
     *
     * @param \DateTime $issuedDate
     * @return TmQualification
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get the issued date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * Set the serial no
     *
     * @param string $serialNo
     * @return TmQualification
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;

        return $this;
    }

    /**
     * Get the serial no
     *
     * @return string
     */
    public function getSerialNo()
    {
        return $this->serialNo;
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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * @return bool
     */
    public function isDeleted()
    {
        return !is_null($this->deletedDate);
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
