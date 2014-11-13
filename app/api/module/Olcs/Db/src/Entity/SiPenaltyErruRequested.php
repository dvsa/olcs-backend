<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiPenaltyErruRequested Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_penalty_erru_requested",
 *    indexes={
 *        @ORM\Index(name="fk_si_penalty_erru_requested_serious_infringement1_idx", 
 *            columns={"serious_infringement_id"}),
 *        @ORM\Index(name="fk_si_penalty_erru_requested_si_penalty_requested_type1_idx", 
 *            columns={"si_penalty_requested_type_id"}),
 *        @ORM\Index(name="fk_si_penalty_erru_requested_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_si_penalty_erru_requested_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenaltyErruRequested implements Interfaces\EntityInterface
{

    /**
     * Si penalty requested type
     *
     * @var \Olcs\Db\Entity\SiPenaltyRequestedType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyRequestedType", fetch="LAZY")
     * @ORM\JoinColumn(name="si_penalty_requested_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siPenaltyRequestedType;

    /**
     * Serious infringement
     *
     * @var \Olcs\Db\Entity\SeriousInfringement
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SeriousInfringement", fetch="LAZY", inversedBy="requestedErrus")
     * @ORM\JoinColumn(name="serious_infringement_id", referencedColumnName="id", nullable=false)
     */
    protected $seriousInfringement;

    /**
     * Duration
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="duration", nullable=true)
     */
    protected $duration;

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
     * Set the si penalty requested type
     *
     * @param \Olcs\Db\Entity\SiPenaltyRequestedType $siPenaltyRequestedType
     * @return SiPenaltyErruRequested
     */
    public function setSiPenaltyRequestedType($siPenaltyRequestedType)
    {
        $this->siPenaltyRequestedType = $siPenaltyRequestedType;

        return $this;
    }

    /**
     * Get the si penalty requested type
     *
     * @return \Olcs\Db\Entity\SiPenaltyRequestedType
     */
    public function getSiPenaltyRequestedType()
    {
        return $this->siPenaltyRequestedType;
    }

    /**
     * Set the serious infringement
     *
     * @param \Olcs\Db\Entity\SeriousInfringement $seriousInfringement
     * @return SiPenaltyErruRequested
     */
    public function setSeriousInfringement($seriousInfringement)
    {
        $this->seriousInfringement = $seriousInfringement;

        return $this;
    }

    /**
     * Get the serious infringement
     *
     * @return \Olcs\Db\Entity\SeriousInfringement
     */
    public function getSeriousInfringement()
    {
        return $this->seriousInfringement;
    }

    /**
     * Set the duration
     *
     * @param int $duration
     * @return SiPenaltyErruRequested
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get the duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
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
