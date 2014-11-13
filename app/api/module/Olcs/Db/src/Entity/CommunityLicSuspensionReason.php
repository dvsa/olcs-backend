<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLicSuspensionReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="community_lic_suspension_reason",
 *    indexes={
 *        @ORM\Index(name="fk_community_lic_suspension_reason_community_lic_suspension_idx", 
 *            columns={"community_lic_suspension_id"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_community_lic_suspension_idx1", 
 *            columns={"reason_id"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicSuspensionReason implements Interfaces\EntityInterface
{

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\CommunityLicSuspensionReasonType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspensionReasonType", fetch="LAZY")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false)
     */
    protected $reason;

    /**
     * Community lic suspension
     *
     * @var \Olcs\Db\Entity\CommunityLicSuspension
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspension", fetch="LAZY")
     * @ORM\JoinColumn(name="community_lic_suspension_id", referencedColumnName="id", nullable=false)
     */
    protected $communityLicSuspension;

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
     * Set the reason
     *
     * @param \Olcs\Db\Entity\CommunityLicSuspensionReasonType $reason
     * @return CommunityLicSuspensionReason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Olcs\Db\Entity\CommunityLicSuspensionReasonType
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the community lic suspension
     *
     * @param \Olcs\Db\Entity\CommunityLicSuspension $communityLicSuspension
     * @return CommunityLicSuspensionReason
     */
    public function setCommunityLicSuspension($communityLicSuspension)
    {
        $this->communityLicSuspension = $communityLicSuspension;

        return $this;
    }

    /**
     * Get the community lic suspension
     *
     * @return \Olcs\Db\Entity\CommunityLicSuspension
     */
    public function getCommunityLicSuspension()
    {
        return $this->communityLicSuspension;
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
