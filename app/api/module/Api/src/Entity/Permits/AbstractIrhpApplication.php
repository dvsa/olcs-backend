<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 *        @ORM\Index(name="ix_irhp_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_irhp_application_cancellation_date", columns={"cancellation_date"}),
 *        @ORM\Index(name="ix_irhp_application_withdrawn_date", columns={"withdrawn_date"}),
 *        @ORM\Index(name="ix_irhp_application_withdraw_reason", columns={"withdraw_reason"}),
 *        @ORM\Index(name="fk_irhp_application_international_journeys",
     *     columns={"international_journeys"}),
 *        @ORM\Index(name="fk_irhp_application_sectors_id", columns={"sectors_id"})
 *    }
 * )
 */
abstract class AbstractIrhpApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

    /**
     * Cancellation date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cancellation_date", nullable=true)
     */
    protected $cancellationDate;

    /**
     * Checked answers
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="checked_answers", nullable=false, options={"default": 0})
     */
    protected $checkedAnswers = 0;

    /**
     * Country
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country",
     *     inversedBy="irhpApplications",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="irhp_application_country_link",
     *     joinColumns={
     *         @ORM\JoinColumn(name="irhp_application_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $countrys;

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
     * Expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="expiry_date", nullable=true)
     */
    protected $expiryDate;

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
     * In scope
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="in_scope", nullable=true, options={"default": 0})
     */
    protected $inScope = 0;

    /**
     * International journeys
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="international_journeys", referencedColumnName="id", nullable=true)
     */
    protected $internationalJourneys;

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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence",
     *     fetch="LAZY",
     *     inversedBy="irhpApplications"
     * )
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Sectors
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\Sectors
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\Sectors", fetch="LAZY")
     * @ORM\JoinColumn(name="sectors_id", referencedColumnName="id", nullable=true)
     */
    protected $sectors;

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
     * Withdraw reason
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdraw_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawReason;

    /**
     * Withdrawn date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="withdrawn_date", nullable=true)
     */
    protected $withdrawnDate;

    /**
     * Answer
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\Answer",
     *     mappedBy="irhpApplication",
     *     indexBy="question_text_id"
     * )
     */
    protected $answers;

    /**
     * Fee
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Fee\Fee", mappedBy="irhpApplication")
     */
    protected $fees;

    /**
     * Irhp permit application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication",
     *     mappedBy="irhpApplication"
     * )
     */
    protected $irhpPermitApplications;

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
        $this->countrys = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->fees = new ArrayCollection();
        $this->irhpPermitApplications = new ArrayCollection();
    }

    /**
     * Set the cancellation date
     *
     * @param \DateTime $cancellationDate new value being set
     *
     * @return IrhpApplication
     */
    public function setCancellationDate($cancellationDate)
    {
        $this->cancellationDate = $cancellationDate;

        return $this;
    }

    /**
     * Get the cancellation date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCancellationDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->cancellationDate);
        }

        return $this->cancellationDate;
    }

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
     * Set the country
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being set as the value
     *
     * @return IrhpApplication
     */
    public function setCountrys($countrys)
    {
        $this->countrys = $countrys;

        return $this;
    }

    /**
     * Get the countrys
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCountrys()
    {
        return $this->countrys;
    }

    /**
     * Add a countrys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being added
     *
     * @return IrhpApplication
     */
    public function addCountrys($countrys)
    {
        if ($countrys instanceof ArrayCollection) {
            $this->countrys = new ArrayCollection(
                array_merge(
                    $this->countrys->toArray(),
                    $countrys->toArray()
                )
            );
        } elseif (!$this->countrys->contains($countrys)) {
            $this->countrys->add($countrys);
        }

        return $this;
    }

    /**
     * Remove a countrys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being removed
     *
     * @return IrhpApplication
     */
    public function removeCountrys($countrys)
    {
        if ($this->countrys->contains($countrys)) {
            $this->countrys->removeElement($countrys);
        }

        return $this;
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
     * Set the expiry date
     *
     * @param \DateTime $expiryDate new value being set
     *
     * @return IrhpApplication
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getExpiryDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->expiryDate);
        }

        return $this->expiryDate;
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
     * Set the in scope
     *
     * @param boolean $inScope new value being set
     *
     * @return IrhpApplication
     */
    public function setInScope($inScope)
    {
        $this->inScope = $inScope;

        return $this;
    }

    /**
     * Get the in scope
     *
     * @return boolean
     */
    public function getInScope()
    {
        return $this->inScope;
    }

    /**
     * Set the international journeys
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $internationalJourneys entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setInternationalJourneys($internationalJourneys)
    {
        $this->internationalJourneys = $internationalJourneys;

        return $this;
    }

    /**
     * Get the international journeys
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getInternationalJourneys()
    {
        return $this->internationalJourneys;
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
     * Set the sectors
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\Sectors $sectors entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setSectors($sectors)
    {
        $this->sectors = $sectors;

        return $this;
    }

    /**
     * Get the sectors
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\Sectors
     */
    public function getSectors()
    {
        return $this->sectors;
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
     * Set the withdraw reason
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $withdrawReason entity being set as the value
     *
     * @return IrhpApplication
     */
    public function setWithdrawReason($withdrawReason)
    {
        $this->withdrawReason = $withdrawReason;

        return $this;
    }

    /**
     * Get the withdraw reason
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getWithdrawReason()
    {
        return $this->withdrawReason;
    }

    /**
     * Set the withdrawn date
     *
     * @param \DateTime $withdrawnDate new value being set
     *
     * @return IrhpApplication
     */
    public function setWithdrawnDate($withdrawnDate)
    {
        $this->withdrawnDate = $withdrawnDate;

        return $this;
    }

    /**
     * Get the withdrawn date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getWithdrawnDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->withdrawnDate);
        }

        return $this->withdrawnDate;
    }

    /**
     * Set the answer
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $answers collection being set as the value
     *
     * @return IrhpApplication
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get the answers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Add a answers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $answers collection being added
     *
     * @return IrhpApplication
     */
    public function addAnswers($answers)
    {
        if ($answers instanceof ArrayCollection) {
            $this->answers = new ArrayCollection(
                array_merge(
                    $this->answers->toArray(),
                    $answers->toArray()
                )
            );
        } elseif (!$this->answers->contains($answers)) {
            $this->answers->add($answers);
        }

        return $this;
    }

    /**
     * Remove a answers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $answers collection being removed
     *
     * @return IrhpApplication
     */
    public function removeAnswers($answers)
    {
        if ($this->answers->contains($answers)) {
            $this->answers->removeElement($answers);
        }

        return $this;
    }

    /**
     * Set the fee
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fees collection being set as the value
     *
     * @return IrhpApplication
     */
    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * Get the fees
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * Add a fees
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fees collection being added
     *
     * @return IrhpApplication
     */
    public function addFees($fees)
    {
        if ($fees instanceof ArrayCollection) {
            $this->fees = new ArrayCollection(
                array_merge(
                    $this->fees->toArray(),
                    $fees->toArray()
                )
            );
        } elseif (!$this->fees->contains($fees)) {
            $this->fees->add($fees);
        }

        return $this;
    }

    /**
     * Remove a fees
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fees collection being removed
     *
     * @return IrhpApplication
     */
    public function removeFees($fees)
    {
        if ($this->fees->contains($fees)) {
            $this->fees->removeElement($fees);
        }

        return $this;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitApplications collection being set as the value
     *
     * @return IrhpApplication
     */
    public function setIrhpPermitApplications($irhpPermitApplications)
    {
        $this->irhpPermitApplications = $irhpPermitApplications;

        return $this;
    }

    /**
     * Get the irhp permit applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitApplications()
    {
        return $this->irhpPermitApplications;
    }

    /**
     * Add a irhp permit applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitApplications collection being added
     *
     * @return IrhpApplication
     */
    public function addIrhpPermitApplications($irhpPermitApplications)
    {
        if ($irhpPermitApplications instanceof ArrayCollection) {
            $this->irhpPermitApplications = new ArrayCollection(
                array_merge(
                    $this->irhpPermitApplications->toArray(),
                    $irhpPermitApplications->toArray()
                )
            );
        } elseif (!$this->irhpPermitApplications->contains($irhpPermitApplications)) {
            $this->irhpPermitApplications->add($irhpPermitApplications);
        }

        return $this;
    }

    /**
     * Remove a irhp permit applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitApplications collection being removed
     *
     * @return IrhpApplication
     */
    public function removeIrhpPermitApplications($irhpPermitApplications)
    {
        if ($this->irhpPermitApplications->contains($irhpPermitApplications)) {
            $this->irhpPermitApplications->removeElement($irhpPermitApplications);
        }

        return $this;
    }
}
