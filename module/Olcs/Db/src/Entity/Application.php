<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Application Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="application",
 *    indexes={
 *        @ORM\Index(name="ix_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_application_interim_status", columns={"interim_status"}),
 *        @ORM\Index(name="ix_application_withdrawn_reason", columns={"withdrawn_reason"}),
 *        @ORM\Index(name="ix_application_goods_or_psv", columns={"goods_or_psv"})
 *    }
 * )
 */
class Application implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\GoodsOrPsvManyToOne,
        Traits\GrantedDateField,
        Traits\IdIdentity,
        Traits\IsMaintenanceSuitableField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceTypeManyToOne,
        Traits\NiFlagField,
        Traits\ReceivedDateField,
        Traits\StatusManyToOne,
        Traits\TotAuthLargeVehiclesField,
        Traits\TotAuthMediumVehiclesField,
        Traits\TotAuthSmallVehiclesField,
        Traits\TotAuthTrailersField,
        Traits\TotAuthVehiclesField,
        Traits\TotCommunityLicencesField,
        Traits\CustomVersionField,
        Traits\WithdrawnDateField,
        Traits\WithdrawnReasonManyToOne;

    /**
     * Administration
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="administration", nullable=true)
     */
    protected $administration;

    /**
     * Bankrupt
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="bankrupt", nullable=true)
     */
    protected $bankrupt;

    /**
     * Convictions confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="convictions_confirmation", nullable=false, options={"default": 0})
     */
    protected $convictionsConfirmation = 0;

    /**
     * Declaration confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="declaration_confirmation", nullable=false, options={"default": 0})
     */
    protected $declarationConfirmation = 0;

    /**
     * Disqualified
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="disqualified", nullable=true)
     */
    protected $disqualified;

    /**
     * Financial evidence uploaded
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="financial_evidence_uploaded", nullable=true)
     */
    protected $financialEvidenceUploaded;

    /**
     * Has entered reg
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="has_entered_reg", nullable=true)
     */
    protected $hasEnteredReg;

    /**
     * Insolvency confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="insolvency_confirmation", nullable=false, options={"default": 0})
     */
    protected $insolvencyConfirmation = 0;

    /**
     * Insolvency details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="insolvency_details", length=4000, nullable=true)
     */
    protected $insolvencyDetails;

    /**
     * Interim auth trailers
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="interim_auth_trailers", nullable=true)
     */
    protected $interimAuthTrailers;

    /**
     * Interim auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="interim_auth_vehicles", nullable=true)
     */
    protected $interimAuthVehicles;

    /**
     * Interim end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="interim_end", nullable=true)
     */
    protected $interimEnd;

    /**
     * Interim reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="interim_reason", length=1000, nullable=true)
     */
    protected $interimReason;

    /**
     * Interim start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="interim_start", nullable=true)
     */
    protected $interimStart;

    /**
     * Interim status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="interim_status", referencedColumnName="id", nullable=true)
     */
    protected $interimStatus;

    /**
     * Is variation
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_variation", nullable=false)
     */
    protected $isVariation;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="applications")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Liquidation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="liquidation", nullable=true)
     */
    protected $liquidation;

    /**
     * Override ooo
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="override_ooo", nullable=false, options={"default": 0})
     */
    protected $overrideOoo = 0;

    /**
     * Prev been at pi
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_at_pi", nullable=true)
     */
    protected $prevBeenAtPi;

    /**
     * Prev been disqualified tc
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_disqualified_tc", nullable=true)
     */
    protected $prevBeenDisqualifiedTc;

    /**
     * Prev been refused
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_refused", nullable=true)
     */
    protected $prevBeenRefused;

    /**
     * Prev been revoked
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_been_revoked", nullable=true)
     */
    protected $prevBeenRevoked;

    /**
     * Prev conviction
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_conviction", nullable=true)
     */
    protected $prevConviction;

    /**
     * Prev had licence
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_had_licence", nullable=true)
     */
    protected $prevHadLicence;

    /**
     * Prev has licence
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_has_licence", nullable=true)
     */
    protected $prevHasLicence;

    /**
     * Prev purchased assets
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="prev_purchased_assets", nullable=true)
     */
    protected $prevPurchasedAssets;

    /**
     * Psv limousines
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_limousines", nullable=true)
     */
    protected $psvLimousines;

    /**
     * Psv medium vhl confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_medium_vhl_confirmation", nullable=true)
     */
    protected $psvMediumVhlConfirmation;

    /**
     * Psv medium vhl notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="psv_medium_vhl_notes", length=1000, nullable=true)
     */
    protected $psvMediumVhlNotes;

    /**
     * Psv no limousine confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_no_limousine_confirmation", nullable=true)
     */
    protected $psvNoLimousineConfirmation;

    /**
     * Psv no small vhl confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_no_small_vhl_confirmation", nullable=true)
     */
    protected $psvNoSmallVhlConfirmation;

    /**
     * Psv only limousines confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_only_limousines_confirmation", nullable=true)
     */
    protected $psvOnlyLimousinesConfirmation;

    /**
     * Psv operate small vhl
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_operate_small_vhl", nullable=true)
     */
    protected $psvOperateSmallVhl;

    /**
     * Psv small vhl confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="psv_small_vhl_confirmation", nullable=true)
     */
    protected $psvSmallVhlConfirmation;

    /**
     * Psv small vhl notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="psv_small_vhl_notes", length=4000, nullable=true)
     */
    protected $psvSmallVhlNotes;

    /**
     * Receivership
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="receivership", nullable=true)
     */
    protected $receivership;

    /**
     * Refused date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="refused_date", nullable=true)
     */
    protected $refusedDate;

    /**
     * Safety confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="safety_confirmation", nullable=false, options={"default": 0})
     */
    protected $safetyConfirmation = 0;

    /**
     * Target completion date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="target_completion_date", nullable=true)
     */
    protected $targetCompletionDate;

    /**
     * Application completion
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ApplicationCompletion", mappedBy="application")
     */
    protected $applicationCompletions;

    /**
     * Operating centre
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ApplicationOperatingCentre", mappedBy="application")
     */
    protected $operatingCentres;

    /**
     * Application organisation person
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ApplicationOrganisationPerson", mappedBy="application")
     */
    protected $applicationOrganisationPersons;

    /**
     * Condition undertaking
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ConditionUndertaking", mappedBy="application")
     */
    protected $conditionUndertakings;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="application")
     */
    protected $documents;

    /**
     * Licence vehicle
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\LicenceVehicle", mappedBy="application")
     */
    protected $licenceVehicles;

    /**
     * Opposition
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Opposition", mappedBy="application")
     */
    protected $oppositions;

    /**
     * Previous conviction
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PreviousConviction", mappedBy="application")
     */
    protected $previousConvictions;

    /**
     * Previous licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PreviousLicence", mappedBy="application")
     */
    protected $previousLicences;

    /**
     * Publication link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PublicationLink", mappedBy="application")
     */
    protected $publicationLinks;

    /**
     * Transport manager
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TransportManagerApplication", mappedBy="application")
     */
    protected $transportManagers;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->applicationCompletions = new ArrayCollection();
        $this->operatingCentres = new ArrayCollection();
        $this->applicationOrganisationPersons = new ArrayCollection();
        $this->conditionUndertakings = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->licenceVehicles = new ArrayCollection();
        $this->oppositions = new ArrayCollection();
        $this->previousConvictions = new ArrayCollection();
        $this->previousLicences = new ArrayCollection();
        $this->publicationLinks = new ArrayCollection();
        $this->transportManagers = new ArrayCollection();
    }

    /**
     * Set the administration
     *
     * @param string $administration
     * @return Application
     */
    public function setAdministration($administration)
    {
        $this->administration = $administration;

        return $this;
    }

    /**
     * Get the administration
     *
     * @return string
     */
    public function getAdministration()
    {
        return $this->administration;
    }

    /**
     * Set the bankrupt
     *
     * @param string $bankrupt
     * @return Application
     */
    public function setBankrupt($bankrupt)
    {
        $this->bankrupt = $bankrupt;

        return $this;
    }

    /**
     * Get the bankrupt
     *
     * @return string
     */
    public function getBankrupt()
    {
        return $this->bankrupt;
    }

    /**
     * Set the convictions confirmation
     *
     * @param string $convictionsConfirmation
     * @return Application
     */
    public function setConvictionsConfirmation($convictionsConfirmation)
    {
        $this->convictionsConfirmation = $convictionsConfirmation;

        return $this;
    }

    /**
     * Get the convictions confirmation
     *
     * @return string
     */
    public function getConvictionsConfirmation()
    {
        return $this->convictionsConfirmation;
    }

    /**
     * Set the declaration confirmation
     *
     * @param string $declarationConfirmation
     * @return Application
     */
    public function setDeclarationConfirmation($declarationConfirmation)
    {
        $this->declarationConfirmation = $declarationConfirmation;

        return $this;
    }

    /**
     * Get the declaration confirmation
     *
     * @return string
     */
    public function getDeclarationConfirmation()
    {
        return $this->declarationConfirmation;
    }

    /**
     * Set the disqualified
     *
     * @param string $disqualified
     * @return Application
     */
    public function setDisqualified($disqualified)
    {
        $this->disqualified = $disqualified;

        return $this;
    }

    /**
     * Get the disqualified
     *
     * @return string
     */
    public function getDisqualified()
    {
        return $this->disqualified;
    }

    /**
     * Set the financial evidence uploaded
     *
     * @param string $financialEvidenceUploaded
     * @return Application
     */
    public function setFinancialEvidenceUploaded($financialEvidenceUploaded)
    {
        $this->financialEvidenceUploaded = $financialEvidenceUploaded;

        return $this;
    }

    /**
     * Get the financial evidence uploaded
     *
     * @return string
     */
    public function getFinancialEvidenceUploaded()
    {
        return $this->financialEvidenceUploaded;
    }

    /**
     * Set the has entered reg
     *
     * @param string $hasEnteredReg
     * @return Application
     */
    public function setHasEnteredReg($hasEnteredReg)
    {
        $this->hasEnteredReg = $hasEnteredReg;

        return $this;
    }

    /**
     * Get the has entered reg
     *
     * @return string
     */
    public function getHasEnteredReg()
    {
        return $this->hasEnteredReg;
    }

    /**
     * Set the insolvency confirmation
     *
     * @param string $insolvencyConfirmation
     * @return Application
     */
    public function setInsolvencyConfirmation($insolvencyConfirmation)
    {
        $this->insolvencyConfirmation = $insolvencyConfirmation;

        return $this;
    }

    /**
     * Get the insolvency confirmation
     *
     * @return string
     */
    public function getInsolvencyConfirmation()
    {
        return $this->insolvencyConfirmation;
    }

    /**
     * Set the insolvency details
     *
     * @param string $insolvencyDetails
     * @return Application
     */
    public function setInsolvencyDetails($insolvencyDetails)
    {
        $this->insolvencyDetails = $insolvencyDetails;

        return $this;
    }

    /**
     * Get the insolvency details
     *
     * @return string
     */
    public function getInsolvencyDetails()
    {
        return $this->insolvencyDetails;
    }

    /**
     * Set the interim auth trailers
     *
     * @param int $interimAuthTrailers
     * @return Application
     */
    public function setInterimAuthTrailers($interimAuthTrailers)
    {
        $this->interimAuthTrailers = $interimAuthTrailers;

        return $this;
    }

    /**
     * Get the interim auth trailers
     *
     * @return int
     */
    public function getInterimAuthTrailers()
    {
        return $this->interimAuthTrailers;
    }

    /**
     * Set the interim auth vehicles
     *
     * @param int $interimAuthVehicles
     * @return Application
     */
    public function setInterimAuthVehicles($interimAuthVehicles)
    {
        $this->interimAuthVehicles = $interimAuthVehicles;

        return $this;
    }

    /**
     * Get the interim auth vehicles
     *
     * @return int
     */
    public function getInterimAuthVehicles()
    {
        return $this->interimAuthVehicles;
    }

    /**
     * Set the interim end
     *
     * @param \DateTime $interimEnd
     * @return Application
     */
    public function setInterimEnd($interimEnd)
    {
        $this->interimEnd = $interimEnd;

        return $this;
    }

    /**
     * Get the interim end
     *
     * @return \DateTime
     */
    public function getInterimEnd()
    {
        return $this->interimEnd;
    }

    /**
     * Set the interim reason
     *
     * @param string $interimReason
     * @return Application
     */
    public function setInterimReason($interimReason)
    {
        $this->interimReason = $interimReason;

        return $this;
    }

    /**
     * Get the interim reason
     *
     * @return string
     */
    public function getInterimReason()
    {
        return $this->interimReason;
    }

    /**
     * Set the interim start
     *
     * @param \DateTime $interimStart
     * @return Application
     */
    public function setInterimStart($interimStart)
    {
        $this->interimStart = $interimStart;

        return $this;
    }

    /**
     * Get the interim start
     *
     * @return \DateTime
     */
    public function getInterimStart()
    {
        return $this->interimStart;
    }

    /**
     * Set the interim status
     *
     * @param \Olcs\Db\Entity\RefData $interimStatus
     * @return Application
     */
    public function setInterimStatus($interimStatus)
    {
        $this->interimStatus = $interimStatus;

        return $this;
    }

    /**
     * Get the interim status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getInterimStatus()
    {
        return $this->interimStatus;
    }

    /**
     * Set the is variation
     *
     * @param boolean $isVariation
     * @return Application
     */
    public function setIsVariation($isVariation)
    {
        $this->isVariation = $isVariation;

        return $this;
    }

    /**
     * Get the is variation
     *
     * @return boolean
     */
    public function getIsVariation()
    {
        return $this->isVariation;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return Application
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the liquidation
     *
     * @param string $liquidation
     * @return Application
     */
    public function setLiquidation($liquidation)
    {
        $this->liquidation = $liquidation;

        return $this;
    }

    /**
     * Get the liquidation
     *
     * @return string
     */
    public function getLiquidation()
    {
        return $this->liquidation;
    }

    /**
     * Set the override ooo
     *
     * @param string $overrideOoo
     * @return Application
     */
    public function setOverrideOoo($overrideOoo)
    {
        $this->overrideOoo = $overrideOoo;

        return $this;
    }

    /**
     * Get the override ooo
     *
     * @return string
     */
    public function getOverrideOoo()
    {
        return $this->overrideOoo;
    }

    /**
     * Set the prev been at pi
     *
     * @param string $prevBeenAtPi
     * @return Application
     */
    public function setPrevBeenAtPi($prevBeenAtPi)
    {
        $this->prevBeenAtPi = $prevBeenAtPi;

        return $this;
    }

    /**
     * Get the prev been at pi
     *
     * @return string
     */
    public function getPrevBeenAtPi()
    {
        return $this->prevBeenAtPi;
    }

    /**
     * Set the prev been disqualified tc
     *
     * @param string $prevBeenDisqualifiedTc
     * @return Application
     */
    public function setPrevBeenDisqualifiedTc($prevBeenDisqualifiedTc)
    {
        $this->prevBeenDisqualifiedTc = $prevBeenDisqualifiedTc;

        return $this;
    }

    /**
     * Get the prev been disqualified tc
     *
     * @return string
     */
    public function getPrevBeenDisqualifiedTc()
    {
        return $this->prevBeenDisqualifiedTc;
    }

    /**
     * Set the prev been refused
     *
     * @param string $prevBeenRefused
     * @return Application
     */
    public function setPrevBeenRefused($prevBeenRefused)
    {
        $this->prevBeenRefused = $prevBeenRefused;

        return $this;
    }

    /**
     * Get the prev been refused
     *
     * @return string
     */
    public function getPrevBeenRefused()
    {
        return $this->prevBeenRefused;
    }

    /**
     * Set the prev been revoked
     *
     * @param string $prevBeenRevoked
     * @return Application
     */
    public function setPrevBeenRevoked($prevBeenRevoked)
    {
        $this->prevBeenRevoked = $prevBeenRevoked;

        return $this;
    }

    /**
     * Get the prev been revoked
     *
     * @return string
     */
    public function getPrevBeenRevoked()
    {
        return $this->prevBeenRevoked;
    }

    /**
     * Set the prev conviction
     *
     * @param string $prevConviction
     * @return Application
     */
    public function setPrevConviction($prevConviction)
    {
        $this->prevConviction = $prevConviction;

        return $this;
    }

    /**
     * Get the prev conviction
     *
     * @return string
     */
    public function getPrevConviction()
    {
        return $this->prevConviction;
    }

    /**
     * Set the prev had licence
     *
     * @param string $prevHadLicence
     * @return Application
     */
    public function setPrevHadLicence($prevHadLicence)
    {
        $this->prevHadLicence = $prevHadLicence;

        return $this;
    }

    /**
     * Get the prev had licence
     *
     * @return string
     */
    public function getPrevHadLicence()
    {
        return $this->prevHadLicence;
    }

    /**
     * Set the prev has licence
     *
     * @param string $prevHasLicence
     * @return Application
     */
    public function setPrevHasLicence($prevHasLicence)
    {
        $this->prevHasLicence = $prevHasLicence;

        return $this;
    }

    /**
     * Get the prev has licence
     *
     * @return string
     */
    public function getPrevHasLicence()
    {
        return $this->prevHasLicence;
    }

    /**
     * Set the prev purchased assets
     *
     * @param string $prevPurchasedAssets
     * @return Application
     */
    public function setPrevPurchasedAssets($prevPurchasedAssets)
    {
        $this->prevPurchasedAssets = $prevPurchasedAssets;

        return $this;
    }

    /**
     * Get the prev purchased assets
     *
     * @return string
     */
    public function getPrevPurchasedAssets()
    {
        return $this->prevPurchasedAssets;
    }

    /**
     * Set the psv limousines
     *
     * @param string $psvLimousines
     * @return Application
     */
    public function setPsvLimousines($psvLimousines)
    {
        $this->psvLimousines = $psvLimousines;

        return $this;
    }

    /**
     * Get the psv limousines
     *
     * @return string
     */
    public function getPsvLimousines()
    {
        return $this->psvLimousines;
    }

    /**
     * Set the psv medium vhl confirmation
     *
     * @param string $psvMediumVhlConfirmation
     * @return Application
     */
    public function setPsvMediumVhlConfirmation($psvMediumVhlConfirmation)
    {
        $this->psvMediumVhlConfirmation = $psvMediumVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv medium vhl confirmation
     *
     * @return string
     */
    public function getPsvMediumVhlConfirmation()
    {
        return $this->psvMediumVhlConfirmation;
    }

    /**
     * Set the psv medium vhl notes
     *
     * @param string $psvMediumVhlNotes
     * @return Application
     */
    public function setPsvMediumVhlNotes($psvMediumVhlNotes)
    {
        $this->psvMediumVhlNotes = $psvMediumVhlNotes;

        return $this;
    }

    /**
     * Get the psv medium vhl notes
     *
     * @return string
     */
    public function getPsvMediumVhlNotes()
    {
        return $this->psvMediumVhlNotes;
    }

    /**
     * Set the psv no limousine confirmation
     *
     * @param string $psvNoLimousineConfirmation
     * @return Application
     */
    public function setPsvNoLimousineConfirmation($psvNoLimousineConfirmation)
    {
        $this->psvNoLimousineConfirmation = $psvNoLimousineConfirmation;

        return $this;
    }

    /**
     * Get the psv no limousine confirmation
     *
     * @return string
     */
    public function getPsvNoLimousineConfirmation()
    {
        return $this->psvNoLimousineConfirmation;
    }

    /**
     * Set the psv no small vhl confirmation
     *
     * @param string $psvNoSmallVhlConfirmation
     * @return Application
     */
    public function setPsvNoSmallVhlConfirmation($psvNoSmallVhlConfirmation)
    {
        $this->psvNoSmallVhlConfirmation = $psvNoSmallVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv no small vhl confirmation
     *
     * @return string
     */
    public function getPsvNoSmallVhlConfirmation()
    {
        return $this->psvNoSmallVhlConfirmation;
    }

    /**
     * Set the psv only limousines confirmation
     *
     * @param string $psvOnlyLimousinesConfirmation
     * @return Application
     */
    public function setPsvOnlyLimousinesConfirmation($psvOnlyLimousinesConfirmation)
    {
        $this->psvOnlyLimousinesConfirmation = $psvOnlyLimousinesConfirmation;

        return $this;
    }

    /**
     * Get the psv only limousines confirmation
     *
     * @return string
     */
    public function getPsvOnlyLimousinesConfirmation()
    {
        return $this->psvOnlyLimousinesConfirmation;
    }

    /**
     * Set the psv operate small vhl
     *
     * @param string $psvOperateSmallVhl
     * @return Application
     */
    public function setPsvOperateSmallVhl($psvOperateSmallVhl)
    {
        $this->psvOperateSmallVhl = $psvOperateSmallVhl;

        return $this;
    }

    /**
     * Get the psv operate small vhl
     *
     * @return string
     */
    public function getPsvOperateSmallVhl()
    {
        return $this->psvOperateSmallVhl;
    }

    /**
     * Set the psv small vhl confirmation
     *
     * @param string $psvSmallVhlConfirmation
     * @return Application
     */
    public function setPsvSmallVhlConfirmation($psvSmallVhlConfirmation)
    {
        $this->psvSmallVhlConfirmation = $psvSmallVhlConfirmation;

        return $this;
    }

    /**
     * Get the psv small vhl confirmation
     *
     * @return string
     */
    public function getPsvSmallVhlConfirmation()
    {
        return $this->psvSmallVhlConfirmation;
    }

    /**
     * Set the psv small vhl notes
     *
     * @param string $psvSmallVhlNotes
     * @return Application
     */
    public function setPsvSmallVhlNotes($psvSmallVhlNotes)
    {
        $this->psvSmallVhlNotes = $psvSmallVhlNotes;

        return $this;
    }

    /**
     * Get the psv small vhl notes
     *
     * @return string
     */
    public function getPsvSmallVhlNotes()
    {
        return $this->psvSmallVhlNotes;
    }

    /**
     * Set the receivership
     *
     * @param string $receivership
     * @return Application
     */
    public function setReceivership($receivership)
    {
        $this->receivership = $receivership;

        return $this;
    }

    /**
     * Get the receivership
     *
     * @return string
     */
    public function getReceivership()
    {
        return $this->receivership;
    }

    /**
     * Set the refused date
     *
     * @param \DateTime $refusedDate
     * @return Application
     */
    public function setRefusedDate($refusedDate)
    {
        $this->refusedDate = $refusedDate;

        return $this;
    }

    /**
     * Get the refused date
     *
     * @return \DateTime
     */
    public function getRefusedDate()
    {
        return $this->refusedDate;
    }

    /**
     * Set the safety confirmation
     *
     * @param string $safetyConfirmation
     * @return Application
     */
    public function setSafetyConfirmation($safetyConfirmation)
    {
        $this->safetyConfirmation = $safetyConfirmation;

        return $this;
    }

    /**
     * Get the safety confirmation
     *
     * @return string
     */
    public function getSafetyConfirmation()
    {
        return $this->safetyConfirmation;
    }

    /**
     * Set the target completion date
     *
     * @param \DateTime $targetCompletionDate
     * @return Application
     */
    public function setTargetCompletionDate($targetCompletionDate)
    {
        $this->targetCompletionDate = $targetCompletionDate;

        return $this;
    }

    /**
     * Get the target completion date
     *
     * @return \DateTime
     */
    public function getTargetCompletionDate()
    {
        return $this->targetCompletionDate;
    }

    /**
     * Set the application completion
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationCompletions
     * @return Application
     */
    public function setApplicationCompletions($applicationCompletions)
    {
        $this->applicationCompletions = $applicationCompletions;

        return $this;
    }

    /**
     * Get the application completions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplicationCompletions()
    {
        return $this->applicationCompletions;
    }

    /**
     * Add a application completions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationCompletions
     * @return Application
     */
    public function addApplicationCompletions($applicationCompletions)
    {
        if ($applicationCompletions instanceof ArrayCollection) {
            $this->applicationCompletions = new ArrayCollection(
                array_merge(
                    $this->applicationCompletions->toArray(),
                    $applicationCompletions->toArray()
                )
            );
        } elseif (!$this->applicationCompletions->contains($applicationCompletions)) {
            $this->applicationCompletions->add($applicationCompletions);
        }

        return $this;
    }

    /**
     * Remove a application completions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationCompletions
     * @return Application
     */
    public function removeApplicationCompletions($applicationCompletions)
    {
        if ($this->applicationCompletions->contains($applicationCompletions)) {
            $this->applicationCompletions->removeElement($applicationCompletions);
        }

        return $this;
    }

    /**
     * Set the operating centre
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Application
     */
    public function setOperatingCentres($operatingCentres)
    {
        $this->operatingCentres = $operatingCentres;

        return $this;
    }

    /**
     * Get the operating centres
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOperatingCentres()
    {
        return $this->operatingCentres;
    }

    /**
     * Add a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Application
     */
    public function addOperatingCentres($operatingCentres)
    {
        if ($operatingCentres instanceof ArrayCollection) {
            $this->operatingCentres = new ArrayCollection(
                array_merge(
                    $this->operatingCentres->toArray(),
                    $operatingCentres->toArray()
                )
            );
        } elseif (!$this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->add($operatingCentres);
        }

        return $this;
    }

    /**
     * Remove a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return Application
     */
    public function removeOperatingCentres($operatingCentres)
    {
        if ($this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->removeElement($operatingCentres);
        }

        return $this;
    }

    /**
     * Set the application organisation person
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationOrganisationPersons
     * @return Application
     */
    public function setApplicationOrganisationPersons($applicationOrganisationPersons)
    {
        $this->applicationOrganisationPersons = $applicationOrganisationPersons;

        return $this;
    }

    /**
     * Get the application organisation persons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplicationOrganisationPersons()
    {
        return $this->applicationOrganisationPersons;
    }

    /**
     * Add a application organisation persons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationOrganisationPersons
     * @return Application
     */
    public function addApplicationOrganisationPersons($applicationOrganisationPersons)
    {
        if ($applicationOrganisationPersons instanceof ArrayCollection) {
            $this->applicationOrganisationPersons = new ArrayCollection(
                array_merge(
                    $this->applicationOrganisationPersons->toArray(),
                    $applicationOrganisationPersons->toArray()
                )
            );
        } elseif (!$this->applicationOrganisationPersons->contains($applicationOrganisationPersons)) {
            $this->applicationOrganisationPersons->add($applicationOrganisationPersons);
        }

        return $this;
    }

    /**
     * Remove a application organisation persons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationOrganisationPersons
     * @return Application
     */
    public function removeApplicationOrganisationPersons($applicationOrganisationPersons)
    {
        if ($this->applicationOrganisationPersons->contains($applicationOrganisationPersons)) {
            $this->applicationOrganisationPersons->removeElement($applicationOrganisationPersons);
        }

        return $this;
    }

    /**
     * Set the condition undertaking
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Application
     */
    public function setConditionUndertakings($conditionUndertakings)
    {
        $this->conditionUndertakings = $conditionUndertakings;

        return $this;
    }

    /**
     * Get the condition undertakings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConditionUndertakings()
    {
        return $this->conditionUndertakings;
    }

    /**
     * Add a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Application
     */
    public function addConditionUndertakings($conditionUndertakings)
    {
        if ($conditionUndertakings instanceof ArrayCollection) {
            $this->conditionUndertakings = new ArrayCollection(
                array_merge(
                    $this->conditionUndertakings->toArray(),
                    $conditionUndertakings->toArray()
                )
            );
        } elseif (!$this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->add($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Remove a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Application
     */
    public function removeConditionUndertakings($conditionUndertakings)
    {
        if ($this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->removeElement($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Application
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get the documents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Application
     */
    public function addDocuments($documents)
    {
        if ($documents instanceof ArrayCollection) {
            $this->documents = new ArrayCollection(
                array_merge(
                    $this->documents->toArray(),
                    $documents->toArray()
                )
            );
        } elseif (!$this->documents->contains($documents)) {
            $this->documents->add($documents);
        }

        return $this;
    }

    /**
     * Remove a documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Application
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }

    /**
     * Set the licence vehicle
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Application
     */
    public function setLicenceVehicles($licenceVehicles)
    {
        $this->licenceVehicles = $licenceVehicles;

        return $this;
    }

    /**
     * Get the licence vehicles
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLicenceVehicles()
    {
        return $this->licenceVehicles;
    }

    /**
     * Add a licence vehicles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Application
     */
    public function addLicenceVehicles($licenceVehicles)
    {
        if ($licenceVehicles instanceof ArrayCollection) {
            $this->licenceVehicles = new ArrayCollection(
                array_merge(
                    $this->licenceVehicles->toArray(),
                    $licenceVehicles->toArray()
                )
            );
        } elseif (!$this->licenceVehicles->contains($licenceVehicles)) {
            $this->licenceVehicles->add($licenceVehicles);
        }

        return $this;
    }

    /**
     * Remove a licence vehicles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $licenceVehicles
     * @return Application
     */
    public function removeLicenceVehicles($licenceVehicles)
    {
        if ($this->licenceVehicles->contains($licenceVehicles)) {
            $this->licenceVehicles->removeElement($licenceVehicles);
        }

        return $this;
    }

    /**
     * Set the opposition
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return Application
     */
    public function setOppositions($oppositions)
    {
        $this->oppositions = $oppositions;

        return $this;
    }

    /**
     * Get the oppositions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOppositions()
    {
        return $this->oppositions;
    }

    /**
     * Add a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return Application
     */
    public function addOppositions($oppositions)
    {
        if ($oppositions instanceof ArrayCollection) {
            $this->oppositions = new ArrayCollection(
                array_merge(
                    $this->oppositions->toArray(),
                    $oppositions->toArray()
                )
            );
        } elseif (!$this->oppositions->contains($oppositions)) {
            $this->oppositions->add($oppositions);
        }

        return $this;
    }

    /**
     * Remove a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return Application
     */
    public function removeOppositions($oppositions)
    {
        if ($this->oppositions->contains($oppositions)) {
            $this->oppositions->removeElement($oppositions);
        }

        return $this;
    }

    /**
     * Set the previous conviction
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return Application
     */
    public function setPreviousConvictions($previousConvictions)
    {
        $this->previousConvictions = $previousConvictions;

        return $this;
    }

    /**
     * Get the previous convictions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPreviousConvictions()
    {
        return $this->previousConvictions;
    }

    /**
     * Add a previous convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return Application
     */
    public function addPreviousConvictions($previousConvictions)
    {
        if ($previousConvictions instanceof ArrayCollection) {
            $this->previousConvictions = new ArrayCollection(
                array_merge(
                    $this->previousConvictions->toArray(),
                    $previousConvictions->toArray()
                )
            );
        } elseif (!$this->previousConvictions->contains($previousConvictions)) {
            $this->previousConvictions->add($previousConvictions);
        }

        return $this;
    }

    /**
     * Remove a previous convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return Application
     */
    public function removePreviousConvictions($previousConvictions)
    {
        if ($this->previousConvictions->contains($previousConvictions)) {
            $this->previousConvictions->removeElement($previousConvictions);
        }

        return $this;
    }

    /**
     * Set the previous licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousLicences
     * @return Application
     */
    public function setPreviousLicences($previousLicences)
    {
        $this->previousLicences = $previousLicences;

        return $this;
    }

    /**
     * Get the previous licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPreviousLicences()
    {
        return $this->previousLicences;
    }

    /**
     * Add a previous licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousLicences
     * @return Application
     */
    public function addPreviousLicences($previousLicences)
    {
        if ($previousLicences instanceof ArrayCollection) {
            $this->previousLicences = new ArrayCollection(
                array_merge(
                    $this->previousLicences->toArray(),
                    $previousLicences->toArray()
                )
            );
        } elseif (!$this->previousLicences->contains($previousLicences)) {
            $this->previousLicences->add($previousLicences);
        }

        return $this;
    }

    /**
     * Remove a previous licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousLicences
     * @return Application
     */
    public function removePreviousLicences($previousLicences)
    {
        if ($this->previousLicences->contains($previousLicences)) {
            $this->previousLicences->removeElement($previousLicences);
        }

        return $this;
    }

    /**
     * Set the publication link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Application
     */
    public function setPublicationLinks($publicationLinks)
    {
        $this->publicationLinks = $publicationLinks;

        return $this;
    }

    /**
     * Get the publication links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPublicationLinks()
    {
        return $this->publicationLinks;
    }

    /**
     * Add a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Application
     */
    public function addPublicationLinks($publicationLinks)
    {
        if ($publicationLinks instanceof ArrayCollection) {
            $this->publicationLinks = new ArrayCollection(
                array_merge(
                    $this->publicationLinks->toArray(),
                    $publicationLinks->toArray()
                )
            );
        } elseif (!$this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->add($publicationLinks);
        }

        return $this;
    }

    /**
     * Remove a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Application
     */
    public function removePublicationLinks($publicationLinks)
    {
        if ($this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->removeElement($publicationLinks);
        }

        return $this;
    }

    /**
     * Set the transport manager
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagers
     * @return Application
     */
    public function setTransportManagers($transportManagers)
    {
        $this->transportManagers = $transportManagers;

        return $this;
    }

    /**
     * Get the transport managers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTransportManagers()
    {
        return $this->transportManagers;
    }

    /**
     * Add a transport managers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagers
     * @return Application
     */
    public function addTransportManagers($transportManagers)
    {
        if ($transportManagers instanceof ArrayCollection) {
            $this->transportManagers = new ArrayCollection(
                array_merge(
                    $this->transportManagers->toArray(),
                    $transportManagers->toArray()
                )
            );
        } elseif (!$this->transportManagers->contains($transportManagers)) {
            $this->transportManagers->add($transportManagers);
        }

        return $this;
    }

    /**
     * Remove a transport managers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagers
     * @return Application
     */
    public function removeTransportManagers($transportManagers)
    {
        if ($this->transportManagers->contains($transportManagers)) {
            $this->transportManagers->removeElement($transportManagers);
        }

        return $this;
    }
}
