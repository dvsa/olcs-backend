<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ContinuationDetail Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="continuation_detail",
 *    indexes={
 *        @ORM\Index(name="ix_continuation_detail_continuation_id", columns={"continuation_id"}),
 *        @ORM\Index(name="ix_continuation_detail_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_continuation_detail_status", columns={"status"}),
 *        @ORM\Index(name="ix_continuation_detail_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_continuation_detail_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_continuation_detail_checklist_document_id",
     *     columns={"checklist_document_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_continuation_detail_licence_id_continuation_id",
     *     columns={"licence_id","continuation_id"})
 *    }
 * )
 */
abstract class AbstractContinuationDetail implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Average balance amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal",
     *     name="average_balance_amount",
     *     precision=10,
     *     scale=2,
     *     nullable=true)
     */
    protected $averageBalanceAmount;

    /**
     * Checklist document
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\Document
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document",
     *     fetch="LAZY",
     *     inversedBy="continuationDetails"
     * )
     * @ORM\JoinColumn(name="checklist_document_id", referencedColumnName="id", nullable=true)
     */
    protected $checklistDocument;

    /**
     * Continuation
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Continuation
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Continuation", fetch="LAZY")
     * @ORM\JoinColumn(name="continuation_id", referencedColumnName="id", nullable=false)
     */
    protected $continuation;

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
     * Financial evidence uploaded
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="financial_evidence_uploaded", nullable=true)
     */
    protected $financialEvidenceUploaded;

    /**
     * Has other finances
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="has_other_finances", nullable=true)
     */
    protected $hasOtherFinances;

    /**
     * Has overdraft
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="has_overdraft", nullable=true)
     */
    protected $hasOverdraft;

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
     * Other finances amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal",
     *     name="other_finances_amount",
     *     precision=10,
     *     scale=2,
     *     nullable=true)
     */
    protected $otherFinancesAmount;

    /**
     * Other finances details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="other_finances_details", length=200, nullable=true)
     */
    protected $otherFinancesDetails;

    /**
     * Overdraft amount
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="overdraft_amount", precision=10, scale=2, nullable=true)
     */
    protected $overdraftAmount;

    /**
     * Received
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="received", nullable=false, options={"default": 0})
     */
    protected $received = 0;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Tot auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_vehicles", nullable=true)
     */
    protected $totAuthVehicles;

    /**
     * Tot community licences
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_community_licences", nullable=true)
     */
    protected $totCommunityLicences;

    /**
     * Tot psv discs
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_psv_discs", nullable=true)
     */
    protected $totPsvDiscs;

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
     * Set the average balance amount
     *
     * @param float $averageBalanceAmount new value being set
     *
     * @return ContinuationDetail
     */
    public function setAverageBalanceAmount($averageBalanceAmount)
    {
        $this->averageBalanceAmount = $averageBalanceAmount;

        return $this;
    }

    /**
     * Get the average balance amount
     *
     * @return float
     */
    public function getAverageBalanceAmount()
    {
        return $this->averageBalanceAmount;
    }

    /**
     * Set the checklist document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $checklistDocument entity being set as the value
     *
     * @return ContinuationDetail
     */
    public function setChecklistDocument($checklistDocument)
    {
        $this->checklistDocument = $checklistDocument;

        return $this;
    }

    /**
     * Get the checklist document
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\Document
     */
    public function getChecklistDocument()
    {
        return $this->checklistDocument;
    }

    /**
     * Set the continuation
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Continuation $continuation entity being set as the value
     *
     * @return ContinuationDetail
     */
    public function setContinuation($continuation)
    {
        $this->continuation = $continuation;

        return $this;
    }

    /**
     * Get the continuation
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Continuation
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return ContinuationDetail
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
     * @return ContinuationDetail
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
     * Set the financial evidence uploaded
     *
     * @param boolean $financialEvidenceUploaded new value being set
     *
     * @return ContinuationDetail
     */
    public function setFinancialEvidenceUploaded($financialEvidenceUploaded)
    {
        $this->financialEvidenceUploaded = $financialEvidenceUploaded;

        return $this;
    }

    /**
     * Get the financial evidence uploaded
     *
     * @return boolean
     */
    public function getFinancialEvidenceUploaded()
    {
        return $this->financialEvidenceUploaded;
    }

    /**
     * Set the has other finances
     *
     * @param string $hasOtherFinances new value being set
     *
     * @return ContinuationDetail
     */
    public function setHasOtherFinances($hasOtherFinances)
    {
        $this->hasOtherFinances = $hasOtherFinances;

        return $this;
    }

    /**
     * Get the has other finances
     *
     * @return string
     */
    public function getHasOtherFinances()
    {
        return $this->hasOtherFinances;
    }

    /**
     * Set the has overdraft
     *
     * @param string $hasOverdraft new value being set
     *
     * @return ContinuationDetail
     */
    public function setHasOverdraft($hasOverdraft)
    {
        $this->hasOverdraft = $hasOverdraft;

        return $this;
    }

    /**
     * Get the has overdraft
     *
     * @return string
     */
    public function getHasOverdraft()
    {
        return $this->hasOverdraft;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return ContinuationDetail
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return ContinuationDetail
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
     * @return ContinuationDetail
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
     * @return ContinuationDetail
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
     * Set the other finances amount
     *
     * @param float $otherFinancesAmount new value being set
     *
     * @return ContinuationDetail
     */
    public function setOtherFinancesAmount($otherFinancesAmount)
    {
        $this->otherFinancesAmount = $otherFinancesAmount;

        return $this;
    }

    /**
     * Get the other finances amount
     *
     * @return float
     */
    public function getOtherFinancesAmount()
    {
        return $this->otherFinancesAmount;
    }

    /**
     * Set the other finances details
     *
     * @param string $otherFinancesDetails new value being set
     *
     * @return ContinuationDetail
     */
    public function setOtherFinancesDetails($otherFinancesDetails)
    {
        $this->otherFinancesDetails = $otherFinancesDetails;

        return $this;
    }

    /**
     * Get the other finances details
     *
     * @return string
     */
    public function getOtherFinancesDetails()
    {
        return $this->otherFinancesDetails;
    }

    /**
     * Set the overdraft amount
     *
     * @param float $overdraftAmount new value being set
     *
     * @return ContinuationDetail
     */
    public function setOverdraftAmount($overdraftAmount)
    {
        $this->overdraftAmount = $overdraftAmount;

        return $this;
    }

    /**
     * Get the overdraft amount
     *
     * @return float
     */
    public function getOverdraftAmount()
    {
        return $this->overdraftAmount;
    }

    /**
     * Set the received
     *
     * @param string $received new value being set
     *
     * @return ContinuationDetail
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get the received
     *
     * @return string
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return ContinuationDetail
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
     * Set the tot auth vehicles
     *
     * @param int $totAuthVehicles new value being set
     *
     * @return ContinuationDetail
     */
    public function setTotAuthVehicles($totAuthVehicles)
    {
        $this->totAuthVehicles = $totAuthVehicles;

        return $this;
    }

    /**
     * Get the tot auth vehicles
     *
     * @return int
     */
    public function getTotAuthVehicles()
    {
        return $this->totAuthVehicles;
    }

    /**
     * Set the tot community licences
     *
     * @param int $totCommunityLicences new value being set
     *
     * @return ContinuationDetail
     */
    public function setTotCommunityLicences($totCommunityLicences)
    {
        $this->totCommunityLicences = $totCommunityLicences;

        return $this;
    }

    /**
     * Get the tot community licences
     *
     * @return int
     */
    public function getTotCommunityLicences()
    {
        return $this->totCommunityLicences;
    }

    /**
     * Set the tot psv discs
     *
     * @param int $totPsvDiscs new value being set
     *
     * @return ContinuationDetail
     */
    public function setTotPsvDiscs($totPsvDiscs)
    {
        $this->totPsvDiscs = $totPsvDiscs;

        return $this;
    }

    /**
     * Get the tot psv discs
     *
     * @return int
     */
    public function getTotPsvDiscs()
    {
        return $this->totPsvDiscs;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return ContinuationDetail
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
