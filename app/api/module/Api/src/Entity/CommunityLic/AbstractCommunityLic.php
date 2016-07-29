<?php

namespace Dvsa\Olcs\Api\Entity\CommunityLic;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLic Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="community_lic",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_community_lic_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_community_lic_status", columns={"status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractCommunityLic implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

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
     * Expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="expired_date", nullable=true)
     */
    protected $expiredDate;

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
     * Issue no
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="issue_no", nullable=true)
     */
    protected $issueNo;

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
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence",
     *     fetch="LAZY",
     *     inversedBy="communityLics"
     * )
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Licence expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="licence_expired_date", nullable=true)
     */
    protected $licenceExpiredDate;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Serial no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_no", nullable=true)
     */
    protected $serialNo;

    /**
     * Serial no prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="serial_no_prefix", length=4, nullable=true)
     */
    protected $serialNoPrefix;

    /**
     * Specified date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="specified_date", nullable=true)
     */
    protected $specifiedDate;

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
     * Community lic suspension
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspension",
     *     mappedBy="communityLic"
     * )
     */
    protected $communityLicSuspensions;

    /**
     * Community lic withdrawal
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawal",
     *     mappedBy="communityLic"
     * )
     */
    protected $communityLicWithdrawals;

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->communityLicSuspensions = new ArrayCollection();
        $this->communityLicWithdrawals = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return CommunityLic
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
     * @return CommunityLic
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
     * Set the expired date
     *
     * @param \DateTime $expiredDate new value being set
     *
     * @return CommunityLic
     */
    public function setExpiredDate($expiredDate)
    {
        $this->expiredDate = $expiredDate;

        return $this;
    }

    /**
     * Get the expired date
     *
     * @return \DateTime
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return CommunityLic
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
     * Set the issue no
     *
     * @param int $issueNo new value being set
     *
     * @return CommunityLic
     */
    public function setIssueNo($issueNo)
    {
        $this->issueNo = $issueNo;

        return $this;
    }

    /**
     * Get the issue no
     *
     * @return int
     */
    public function getIssueNo()
    {
        return $this->issueNo;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return CommunityLic
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
     * @return CommunityLic
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
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence entity being set as the value
     *
     * @return CommunityLic
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
     * Set the licence expired date
     *
     * @param \DateTime $licenceExpiredDate new value being set
     *
     * @return CommunityLic
     */
    public function setLicenceExpiredDate($licenceExpiredDate)
    {
        $this->licenceExpiredDate = $licenceExpiredDate;

        return $this;
    }

    /**
     * Get the licence expired date
     *
     * @return \DateTime
     */
    public function getLicenceExpiredDate()
    {
        return $this->licenceExpiredDate;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey new value being set
     *
     * @return CommunityLic
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
     * Set the serial no
     *
     * @param int $serialNo new value being set
     *
     * @return CommunityLic
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;

        return $this;
    }

    /**
     * Get the serial no
     *
     * @return int
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * Set the serial no prefix
     *
     * @param string $serialNoPrefix new value being set
     *
     * @return CommunityLic
     */
    public function setSerialNoPrefix($serialNoPrefix)
    {
        $this->serialNoPrefix = $serialNoPrefix;

        return $this;
    }

    /**
     * Get the serial no prefix
     *
     * @return string
     */
    public function getSerialNoPrefix()
    {
        return $this->serialNoPrefix;
    }

    /**
     * Set the specified date
     *
     * @param \DateTime $specifiedDate new value being set
     *
     * @return CommunityLic
     */
    public function setSpecifiedDate($specifiedDate)
    {
        $this->specifiedDate = $specifiedDate;

        return $this;
    }

    /**
     * Get the specified date
     *
     * @return \DateTime
     */
    public function getSpecifiedDate()
    {
        return $this->specifiedDate;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return CommunityLic
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
     * @return CommunityLic
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
     * Set the community lic suspension
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLicSuspensions collection being set as the value
     *
     * @return CommunityLic
     */
    public function setCommunityLicSuspensions($communityLicSuspensions)
    {
        $this->communityLicSuspensions = $communityLicSuspensions;

        return $this;
    }

    /**
     * Get the community lic suspensions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCommunityLicSuspensions()
    {
        return $this->communityLicSuspensions;
    }

    /**
     * Add a community lic suspensions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLicSuspensions collection being added
     *
     * @return CommunityLic
     */
    public function addCommunityLicSuspensions($communityLicSuspensions)
    {
        if ($communityLicSuspensions instanceof ArrayCollection) {
            $this->communityLicSuspensions = new ArrayCollection(
                array_merge(
                    $this->communityLicSuspensions->toArray(),
                    $communityLicSuspensions->toArray()
                )
            );
        } elseif (!$this->communityLicSuspensions->contains($communityLicSuspensions)) {
            $this->communityLicSuspensions->add($communityLicSuspensions);
        }

        return $this;
    }

    /**
     * Remove a community lic suspensions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLicSuspensions collection being removed
     *
     * @return CommunityLic
     */
    public function removeCommunityLicSuspensions($communityLicSuspensions)
    {
        if ($this->communityLicSuspensions->contains($communityLicSuspensions)) {
            $this->communityLicSuspensions->removeElement($communityLicSuspensions);
        }

        return $this;
    }

    /**
     * Set the community lic withdrawal
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLicWithdrawals collection being set as the value
     *
     * @return CommunityLic
     */
    public function setCommunityLicWithdrawals($communityLicWithdrawals)
    {
        $this->communityLicWithdrawals = $communityLicWithdrawals;

        return $this;
    }

    /**
     * Get the community lic withdrawals
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCommunityLicWithdrawals()
    {
        return $this->communityLicWithdrawals;
    }

    /**
     * Add a community lic withdrawals
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLicWithdrawals collection being added
     *
     * @return CommunityLic
     */
    public function addCommunityLicWithdrawals($communityLicWithdrawals)
    {
        if ($communityLicWithdrawals instanceof ArrayCollection) {
            $this->communityLicWithdrawals = new ArrayCollection(
                array_merge(
                    $this->communityLicWithdrawals->toArray(),
                    $communityLicWithdrawals->toArray()
                )
            );
        } elseif (!$this->communityLicWithdrawals->contains($communityLicWithdrawals)) {
            $this->communityLicWithdrawals->add($communityLicWithdrawals);
        }

        return $this;
    }

    /**
     * Remove a community lic withdrawals
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $communityLicWithdrawals collection being removed
     *
     * @return CommunityLic
     */
    public function removeCommunityLicWithdrawals($communityLicWithdrawals)
    {
        if ($this->communityLicWithdrawals->contains($communityLicWithdrawals)) {
            $this->communityLicWithdrawals->removeElement($communityLicWithdrawals);
        }

        return $this;
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
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
