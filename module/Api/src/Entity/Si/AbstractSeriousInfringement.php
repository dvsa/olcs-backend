<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\SoftDeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SeriousInfringement Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="serious_infringement",
 *    indexes={
 *        @ORM\Index(name="ix_serious_infringement_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_serious_infringement_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_serious_infringement_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_serious_infringement_member_state_code", columns={"member_state_code"}),
 *        @ORM\Index(name="ix_serious_infringement_si_category_id", columns={"si_category_id"}),
 *        @ORM\Index(name="ix_serious_infringement_si_category_type_id",
     *     columns={"si_category_type_id"})
 *    }
 * )
 */
abstract class AbstractSeriousInfringement implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;
    use SoftDeletableTrait;

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases",
     *     fetch="LAZY",
     *     inversedBy="seriousInfringements"
     * )
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Check date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="check_date", nullable=true)
     */
    protected $checkDate;

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
     * Infringement date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="infringement_date", nullable=true)
     */
    protected $infringementDate;

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
     * Member state code
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\Country
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country", fetch="LAZY")
     * @ORM\JoinColumn(name="member_state_code", referencedColumnName="id", nullable=true)
     */
    protected $memberStateCode;

    /**
     * Notification number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notification_number", length=36, nullable=true)
     */
    protected $notificationNumber;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Olbs type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_type", length=48, nullable=true)
     */
    protected $olbsType;

    /**
     * Reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reason", length=500, nullable=true)
     */
    protected $reason;

    /**
     * Si category
     *
     * @var \Dvsa\Olcs\Api\Entity\Si\SiCategory
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Si\SiCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="si_category_id", referencedColumnName="id", nullable=false)
     */
    protected $siCategory;

    /**
     * Si category type
     *
     * @var \Dvsa\Olcs\Api\Entity\Si\SiCategoryType
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Si\SiCategoryType", fetch="LAZY")
     * @ORM\JoinColumn(name="si_category_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siCategoryType;

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
     * Applied penaltie
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Si\SiPenalty", mappedBy="seriousInfringement")
     */
    protected $appliedPenalties;

    /**
     * Imposed erru
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruImposed",
     *     mappedBy="seriousInfringement",
     *     cascade={"persist"}
     * )
     */
    protected $imposedErrus;

    /**
     * Requested erru
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruRequested",
     *     mappedBy="seriousInfringement",
     *     cascade={"persist"}
     * )
     */
    protected $requestedErrus;

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
        $this->appliedPenalties = new ArrayCollection();
        $this->imposedErrus = new ArrayCollection();
        $this->requestedErrus = new ArrayCollection();
    }

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case entity being set as the value
     *
     * @return SeriousInfringement
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the check date
     *
     * @param \DateTime $checkDate new value being set
     *
     * @return SeriousInfringement
     */
    public function setCheckDate($checkDate)
    {
        $this->checkDate = $checkDate;

        return $this;
    }

    /**
     * Get the check date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime|string

     */
    public function getCheckDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->checkDate);
        }

        return $this->checkDate;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return SeriousInfringement
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
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return SeriousInfringement
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
     * Set the infringement date
     *
     * @param \DateTime $infringementDate new value being set
     *
     * @return SeriousInfringement
     */
    public function setInfringementDate($infringementDate)
    {
        $this->infringementDate = $infringementDate;

        return $this;
    }

    /**
     * Get the infringement date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime|string

     */
    public function getInfringementDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->infringementDate);
        }

        return $this->infringementDate;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return SeriousInfringement
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
     * Set the member state code
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\Country $memberStateCode entity being set as the value
     *
     * @return SeriousInfringement
     */
    public function setMemberStateCode($memberStateCode)
    {
        $this->memberStateCode = $memberStateCode;

        return $this;
    }

    /**
     * Get the member state code
     *
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\Country
     */
    public function getMemberStateCode()
    {
        return $this->memberStateCode;
    }

    /**
     * Set the notification number
     *
     * @param string $notificationNumber new value being set
     *
     * @return SeriousInfringement
     */
    public function setNotificationNumber($notificationNumber)
    {
        $this->notificationNumber = $notificationNumber;

        return $this;
    }

    /**
     * Get the notification number
     *
     * @return string
     */
    public function getNotificationNumber()
    {
        return $this->notificationNumber;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey new value being set
     *
     * @return SeriousInfringement
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
     * Set the olbs type
     *
     * @param string $olbsType new value being set
     *
     * @return SeriousInfringement
     */
    public function setOlbsType($olbsType)
    {
        $this->olbsType = $olbsType;

        return $this;
    }

    /**
     * Get the olbs type
     *
     * @return string
     */
    public function getOlbsType()
    {
        return $this->olbsType;
    }

    /**
     * Set the reason
     *
     * @param string $reason new value being set
     *
     * @return SeriousInfringement
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the si category
     *
     * @param \Dvsa\Olcs\Api\Entity\Si\SiCategory $siCategory entity being set as the value
     *
     * @return SeriousInfringement
     */
    public function setSiCategory($siCategory)
    {
        $this->siCategory = $siCategory;

        return $this;
    }

    /**
     * Get the si category
     *
     * @return \Dvsa\Olcs\Api\Entity\Si\SiCategory
     */
    public function getSiCategory()
    {
        return $this->siCategory;
    }

    /**
     * Set the si category type
     *
     * @param \Dvsa\Olcs\Api\Entity\Si\SiCategoryType $siCategoryType entity being set as the value
     *
     * @return SeriousInfringement
     */
    public function setSiCategoryType($siCategoryType)
    {
        $this->siCategoryType = $siCategoryType;

        return $this;
    }

    /**
     * Get the si category type
     *
     * @return \Dvsa\Olcs\Api\Entity\Si\SiCategoryType
     */
    public function getSiCategoryType()
    {
        return $this->siCategoryType;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return SeriousInfringement
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
     * Set the applied penaltie
     *
     * @param ArrayCollection $appliedPenalties collection being set as the value
     *
     * @return SeriousInfringement
     */
    public function setAppliedPenalties($appliedPenalties)
    {
        $this->appliedPenalties = $appliedPenalties;

        return $this;
    }

    /**
     * Get the applied penalties
     *
     * @return ArrayCollection
     */
    public function getAppliedPenalties()
    {
        return $this->appliedPenalties;
    }

    /**
     * Add a applied penalties
     *
     * @param ArrayCollection|mixed $appliedPenalties collection being added
     *
     * @return SeriousInfringement
     */
    public function addAppliedPenalties($appliedPenalties)
    {
        if ($appliedPenalties instanceof ArrayCollection) {
            $this->appliedPenalties = new ArrayCollection(
                array_merge(
                    $this->appliedPenalties->toArray(),
                    $appliedPenalties->toArray()
                )
            );
        } elseif (!$this->appliedPenalties->contains($appliedPenalties)) {
            $this->appliedPenalties->add($appliedPenalties);
        }

        return $this;
    }

    /**
     * Remove a applied penalties
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $appliedPenalties collection being removed
     *
     * @return SeriousInfringement
     */
    public function removeAppliedPenalties($appliedPenalties)
    {
        if ($this->appliedPenalties->contains($appliedPenalties)) {
            $this->appliedPenalties->removeElement($appliedPenalties);
        }

        return $this;
    }

    /**
     * Set the imposed erru
     *
     * @param ArrayCollection $imposedErrus collection being set as the value
     *
     * @return SeriousInfringement
     */
    public function setImposedErrus($imposedErrus)
    {
        $this->imposedErrus = $imposedErrus;

        return $this;
    }

    /**
     * Get the imposed errus
     *
     * @return ArrayCollection
     */
    public function getImposedErrus()
    {
        return $this->imposedErrus;
    }

    /**
     * Add a imposed errus
     *
     * @param ArrayCollection|mixed $imposedErrus collection being added
     *
     * @return SeriousInfringement
     */
    public function addImposedErrus($imposedErrus)
    {
        if ($imposedErrus instanceof ArrayCollection) {
            $this->imposedErrus = new ArrayCollection(
                array_merge(
                    $this->imposedErrus->toArray(),
                    $imposedErrus->toArray()
                )
            );
        } elseif (!$this->imposedErrus->contains($imposedErrus)) {
            $this->imposedErrus->add($imposedErrus);
        }

        return $this;
    }

    /**
     * Remove a imposed errus
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $imposedErrus collection being removed
     *
     * @return SeriousInfringement
     */
    public function removeImposedErrus($imposedErrus)
    {
        if ($this->imposedErrus->contains($imposedErrus)) {
            $this->imposedErrus->removeElement($imposedErrus);
        }

        return $this;
    }

    /**
     * Set the requested erru
     *
     * @param ArrayCollection $requestedErrus collection being set as the value
     *
     * @return SeriousInfringement
     */
    public function setRequestedErrus($requestedErrus)
    {
        $this->requestedErrus = $requestedErrus;

        return $this;
    }

    /**
     * Get the requested errus
     *
     * @return ArrayCollection
     */
    public function getRequestedErrus()
    {
        return $this->requestedErrus;
    }

    /**
     * Add a requested errus
     *
     * @param ArrayCollection|mixed $requestedErrus collection being added
     *
     * @return SeriousInfringement
     */
    public function addRequestedErrus($requestedErrus)
    {
        if ($requestedErrus instanceof ArrayCollection) {
            $this->requestedErrus = new ArrayCollection(
                array_merge(
                    $this->requestedErrus->toArray(),
                    $requestedErrus->toArray()
                )
            );
        } elseif (!$this->requestedErrus->contains($requestedErrus)) {
            $this->requestedErrus->add($requestedErrus);
        }

        return $this;
    }

    /**
     * Remove a requested errus
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $requestedErrus collection being removed
     *
     * @return SeriousInfringement
     */
    public function removeRequestedErrus($requestedErrus)
    {
        if ($this->requestedErrus->contains($requestedErrus)) {
            $this->requestedErrus->removeElement($requestedErrus);
        }

        return $this;
    }
}
