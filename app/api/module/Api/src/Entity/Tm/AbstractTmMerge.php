<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TmMerge Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="tm_merge",
 *    indexes={
 *        @ORM\Index(name="ix_tm_merge_tm_from_id", columns={"tm_from_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_to_id", columns={"tm_to_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_application_id", columns={"tm_application_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_licence_id", columns={"tm_licence_id"}),
 *        @ORM\Index(name="ix_tm_merge_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_tm_merge_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_tm_merge_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractTmMerge
{

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
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
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Tm application
     *
     * @var \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication")
     * @ORM\JoinColumn(name="tm_application_id", referencedColumnName="id", nullable=true)
     */
    protected $tmApplication;

    /**
     * Tm from
     *
     * @var \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManager")
     * @ORM\JoinColumn(name="tm_from_id", referencedColumnName="id", nullable=false)
     */
    protected $tmFrom;

    /**
     * Tm licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence")
     * @ORM\JoinColumn(name="tm_licence_id", referencedColumnName="id", nullable=true)
     */
    protected $tmLicence;

    /**
     * Tm to
     *
     * @var \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManager")
     * @ORM\JoinColumn(name="tm_to_id", referencedColumnName="id", nullable=false)
     */
    protected $tmTo;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Version
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     */
    protected $version = 1;

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return TmMerge
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
     * @param \DateTime $createdOn
     * @return TmMerge
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
     * Set the id
     *
     * @param int $id
     * @return TmMerge
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return TmMerge
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
     * @param \DateTime $lastModifiedOn
     * @return TmMerge
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
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return TmMerge
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the tm application
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication $tmApplication
     * @return TmMerge
     */
    public function setTmApplication($tmApplication)
    {
        $this->tmApplication = $tmApplication;

        return $this;
    }

    /**
     * Get the tm application
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication
     */
    public function getTmApplication()
    {
        return $this->tmApplication;
    }

    /**
     * Set the tm from
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManager $tmFrom
     * @return TmMerge
     */
    public function setTmFrom($tmFrom)
    {
        $this->tmFrom = $tmFrom;

        return $this;
    }

    /**
     * Get the tm from
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     */
    public function getTmFrom()
    {
        return $this->tmFrom;
    }

    /**
     * Set the tm licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence $tmLicence
     * @return TmMerge
     */
    public function setTmLicence($tmLicence)
    {
        $this->tmLicence = $tmLicence;

        return $this;
    }

    /**
     * Get the tm licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence
     */
    public function getTmLicence()
    {
        return $this->tmLicence;
    }

    /**
     * Set the tm to
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManager $tmTo
     * @return TmMerge
     */
    public function setTmTo($tmTo)
    {
        $this->tmTo = $tmTo;

        return $this;
    }

    /**
     * Get the tm to
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     */
    public function getTmTo()
    {
        return $this->tmTo;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return TmMerge
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
}
