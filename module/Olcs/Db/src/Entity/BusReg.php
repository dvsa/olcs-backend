<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusReg Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="bus_reg",
 *    indexes={
 *        @ORM\Index(name="fk_bus_reg_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_bus_reg_bus_notice_period1_idx", columns={"bus_notice_period_id"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data1_idx", columns={"subsidised"}),
 *        @ORM\Index(name="fk_bus_reg_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_bus_reg_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_reg_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data2_idx", columns={"withdrawn_reason"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data3_idx", columns={"status"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data4_idx", columns={"revert_status"}),
 *        @ORM\Index(name="fk_bus_reg_bus_reg_idx", columns={"parent_id"})
 *    }
 * )
 */
class BusReg implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EffectiveDateField,
        Traits\EndDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OperatingCentreManyToOneAlt1,
        Traits\ServiceNo70Field,
        Traits\StatusManyToOne,
        Traits\CustomVersionField,
        Traits\WithdrawnReasonManyToOne;

    /**
     * Application signed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="application_signed", nullable=false, options={"default": 0})
     */
    protected $applicationSigned;

    /**
     * Bus notice period
     *
     * @var \Olcs\Db\Entity\BusNoticePeriod
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusNoticePeriod")
     * @ORM\JoinColumn(name="bus_notice_period_id", referencedColumnName="id", nullable=false)
     */
    protected $busNoticePeriod;

    /**
     * Bus service type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\BusServiceType", inversedBy="busRegs")
     * @ORM\JoinTable(name="bus_reg_bus_service_type",
     *     joinColumns={
     *         @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="bus_service_type_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $busServiceTypes;

    /**
     * Copied to la pte
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="copied_to_la_pte", nullable=false, options={"default": 0})
     */
    protected $copiedToLaPte;

    /**
     * Ebsr refresh
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="ebsr_refresh", nullable=false, options={"default": 0})
     */
    protected $ebsrRefresh;

    /**
     * Finish point
     *
     * @var string
     *
     * @ORM\Column(type="string", name="finish_point", length=100, nullable=true)
     */
    protected $finishPoint;

    /**
     * Has manoeuvre
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="has_manoeuvre", nullable=false, options={"default": 0})
     */
    protected $hasManoeuvre;

    /**
     * Has not fixed stop
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="has_not_fixed_stop", nullable=false, options={"default": 0})
     */
    protected $hasNotFixedStop;

    /**
     * Is quality contract
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_quality_contract", nullable=false, options={"default": 0})
     */
    protected $isQualityContract;

    /**
     * Is quality partnership
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_quality_partnership", nullable=false, options={"default": 0})
     */
    protected $isQualityPartnership;

    /**
     * Is short notice
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_short_notice", nullable=false, options={"default": 0})
     */
    protected $isShortNotice;

    /**
     * Is txc app
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_txc_app", nullable=false, options={"default": 0})
     */
    protected $isTxcApp;

    /**
     * La short note
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="la_short_note", nullable=false, options={"default": 0})
     */
    protected $laShortNote;

    /**
     * Local authority
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\LocalAuthority", inversedBy="busRegs")
     * @ORM\JoinTable(name="bus_reg_local_auth",
     *     joinColumns={
     *         @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="local_authority_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $localAuthoritys;

    /**
     * Manoeuvre detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="manoeuvre_detail", length=255, nullable=true)
     */
    protected $manoeuvreDetail;

    /**
     * Map supplied
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="map_supplied", nullable=false, options={"default": 0})
     */
    protected $mapSupplied;

    /**
     * Need new stop
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="need_new_stop", nullable=false, options={"default": 0})
     */
    protected $needNewStop;

    /**
     * New stop detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="new_stop_detail", length=255, nullable=true)
     */
    protected $newStopDetail;

    /**
     * Not fixed stop detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="not_fixed_stop_detail", length=255, nullable=true)
     */
    protected $notFixedStopDetail;

    /**
     * Op notified la pte
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="op_notified_la_pte", nullable=false, options={"default": 0})
     */
    protected $opNotifiedLaPte;

    /**
     * Organisation email
     *
     * @var string
     *
     * @ORM\Column(type="string", name="organisation_email", length=255, nullable=true)
     */
    protected $organisationEmail;

    /**
     * Other details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="other_details", length=800, nullable=true)
     */
    protected $otherDetails;

    /**
     * Parent
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusReg")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Quality contract details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="quality_contract_details", length=4000, nullable=true)
     */
    protected $qualityContractDetails;

    /**
     * Quality partnership details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="quality_partnership_details", length=4000, nullable=true)
     */
    protected $qualityPartnershipDetails;

    /**
     * Quality partnership facilities used
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="quality_partnership_facilities_used", nullable=false, options={"default": 0})
     */
    protected $qualityPartnershipFacilitiesUsed;

    /**
     * Reason cancelled
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reason_cancelled", length=255, nullable=true)
     */
    protected $reasonCancelled;

    /**
     * Reason refused
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reason_refused", length=255, nullable=true)
     */
    protected $reasonRefused;

    /**
     * Reason sn refused
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reason_sn_refused", length=255, nullable=true)
     */
    protected $reasonSnRefused;

    /**
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="received_date", nullable=true)
     */
    protected $receivedDate;

    /**
     * Reg no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reg_no", length=70, nullable=false)
     */
    protected $regNo;

    /**
     * Revert status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="revert_status", referencedColumnName="id", nullable=false)
     */
    protected $revertStatus;

    /**
     * Route description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="route_description", length=1000, nullable=true)
     */
    protected $routeDescription;

    /**
     * Route no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="route_no", nullable=false)
     */
    protected $routeNo;

    /**
     * Short notice refused
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="short_notice_refused", nullable=false, options={"default": 0})
     */
    protected $shortNoticeRefused;

    /**
     * Start point
     *
     * @var string
     *
     * @ORM\Column(type="string", name="start_point", length=100, nullable=true)
     */
    protected $startPoint;

    /**
     * Stopping arrangements
     *
     * @var string
     *
     * @ORM\Column(type="string", name="stopping_arrangements", length=800, nullable=true)
     */
    protected $stoppingArrangements;

    /**
     * Subsidised
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="subsidised", referencedColumnName="id", nullable=false)
     */
    protected $subsidised;

    /**
     * Subsidy detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="subsidy_detail", length=255, nullable=true)
     */
    protected $subsidyDetail;

    /**
     * Timetable acceptable
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="timetable_acceptable", nullable=false, options={"default": 0})
     */
    protected $timetableAcceptable;

    /**
     * Traffic area
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TrafficArea", inversedBy="busRegs")
     * @ORM\JoinTable(name="bus_reg_traffic_area",
     *     joinColumns={
     *         @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $trafficAreas;

    /**
     * Trc condition checked
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="trc_condition_checked", nullable=false, options={"default": 0})
     */
    protected $trcConditionChecked;

    /**
     * Trc notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="trc_notes", length=255, nullable=true)
     */
    protected $trcNotes;

    /**
     * Txc app type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_app_type", length=20, nullable=true)
     */
    protected $txcAppType;

    /**
     * Use all stops
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="use_all_stops", nullable=false, options={"default": 0})
     */
    protected $useAllStops;

    /**
     * Variation no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="variation_no", nullable=false, options={"default": 0})
     */
    protected $variationNo;

    /**
     * Variation reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\VariationReason", inversedBy="busRegs")
     * @ORM\JoinTable(name="bus_reg_variation_reason",
     *     joinColumns={
     *         @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="variation_reason_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $variationReasons;

    /**
     * Via
     *
     * @var string
     *
     * @ORM\Column(type="string", name="via", length=255, nullable=true)
     */
    protected $via;

    /**
     * Other service
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\BusRegOtherService", mappedBy="busReg")
     */
    protected $otherServices;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="busReg")
     */
    protected $documents;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->variationReasons = new ArrayCollection();
        $this->trafficAreas = new ArrayCollection();
        $this->localAuthoritys = new ArrayCollection();
        $this->busServiceTypes = new ArrayCollection();
        $this->otherServices = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * Set the application signed
     *
     * @param string $applicationSigned
     * @return BusReg
     */
    public function setApplicationSigned($applicationSigned)
    {
        $this->applicationSigned = $applicationSigned;

        return $this;
    }

    /**
     * Get the application signed
     *
     * @return string
     */
    public function getApplicationSigned()
    {
        return $this->applicationSigned;
    }

    /**
     * Set the bus notice period
     *
     * @param \Olcs\Db\Entity\BusNoticePeriod $busNoticePeriod
     * @return BusReg
     */
    public function setBusNoticePeriod($busNoticePeriod)
    {
        $this->busNoticePeriod = $busNoticePeriod;

        return $this;
    }

    /**
     * Get the bus notice period
     *
     * @return \Olcs\Db\Entity\BusNoticePeriod
     */
    public function getBusNoticePeriod()
    {
        return $this->busNoticePeriod;
    }

    /**
     * Set the bus service type
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busServiceTypes
     * @return BusReg
     */
    public function setBusServiceTypes($busServiceTypes)
    {
        $this->busServiceTypes = $busServiceTypes;

        return $this;
    }

    /**
     * Get the bus service types
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBusServiceTypes()
    {
        return $this->busServiceTypes;
    }

    /**
     * Add a bus service types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busServiceTypes
     * @return BusReg
     */
    public function addBusServiceTypes($busServiceTypes)
    {
        if ($busServiceTypes instanceof ArrayCollection) {
            $this->busServiceTypes = new ArrayCollection(
                array_merge(
                    $this->busServiceTypes->toArray(),
                    $busServiceTypes->toArray()
                )
            );
        } elseif (!$this->busServiceTypes->contains($busServiceTypes)) {
            $this->busServiceTypes->add($busServiceTypes);
        }

        return $this;
    }

    /**
     * Remove a bus service types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $busServiceTypes
     * @return BusReg
     */
    public function removeBusServiceTypes($busServiceTypes)
    {
        if ($this->busServiceTypes->contains($busServiceTypes)) {
            $this->busServiceTypes->removeElement($busServiceTypes);
        }

        return $this;
    }

    /**
     * Set the copied to la pte
     *
     * @param string $copiedToLaPte
     * @return BusReg
     */
    public function setCopiedToLaPte($copiedToLaPte)
    {
        $this->copiedToLaPte = $copiedToLaPte;

        return $this;
    }

    /**
     * Get the copied to la pte
     *
     * @return string
     */
    public function getCopiedToLaPte()
    {
        return $this->copiedToLaPte;
    }

    /**
     * Set the ebsr refresh
     *
     * @param boolean $ebsrRefresh
     * @return BusReg
     */
    public function setEbsrRefresh($ebsrRefresh)
    {
        $this->ebsrRefresh = $ebsrRefresh;

        return $this;
    }

    /**
     * Get the ebsr refresh
     *
     * @return boolean
     */
    public function getEbsrRefresh()
    {
        return $this->ebsrRefresh;
    }

    /**
     * Set the finish point
     *
     * @param string $finishPoint
     * @return BusReg
     */
    public function setFinishPoint($finishPoint)
    {
        $this->finishPoint = $finishPoint;

        return $this;
    }

    /**
     * Get the finish point
     *
     * @return string
     */
    public function getFinishPoint()
    {
        return $this->finishPoint;
    }

    /**
     * Set the has manoeuvre
     *
     * @param string $hasManoeuvre
     * @return BusReg
     */
    public function setHasManoeuvre($hasManoeuvre)
    {
        $this->hasManoeuvre = $hasManoeuvre;

        return $this;
    }

    /**
     * Get the has manoeuvre
     *
     * @return string
     */
    public function getHasManoeuvre()
    {
        return $this->hasManoeuvre;
    }

    /**
     * Set the has not fixed stop
     *
     * @param string $hasNotFixedStop
     * @return BusReg
     */
    public function setHasNotFixedStop($hasNotFixedStop)
    {
        $this->hasNotFixedStop = $hasNotFixedStop;

        return $this;
    }

    /**
     * Get the has not fixed stop
     *
     * @return string
     */
    public function getHasNotFixedStop()
    {
        return $this->hasNotFixedStop;
    }

    /**
     * Set the is quality contract
     *
     * @param string $isQualityContract
     * @return BusReg
     */
    public function setIsQualityContract($isQualityContract)
    {
        $this->isQualityContract = $isQualityContract;

        return $this;
    }

    /**
     * Get the is quality contract
     *
     * @return string
     */
    public function getIsQualityContract()
    {
        return $this->isQualityContract;
    }

    /**
     * Set the is quality partnership
     *
     * @param string $isQualityPartnership
     * @return BusReg
     */
    public function setIsQualityPartnership($isQualityPartnership)
    {
        $this->isQualityPartnership = $isQualityPartnership;

        return $this;
    }

    /**
     * Get the is quality partnership
     *
     * @return string
     */
    public function getIsQualityPartnership()
    {
        return $this->isQualityPartnership;
    }

    /**
     * Set the is short notice
     *
     * @param string $isShortNotice
     * @return BusReg
     */
    public function setIsShortNotice($isShortNotice)
    {
        $this->isShortNotice = $isShortNotice;

        return $this;
    }

    /**
     * Get the is short notice
     *
     * @return string
     */
    public function getIsShortNotice()
    {
        return $this->isShortNotice;
    }

    /**
     * Set the is txc app
     *
     * @param string $isTxcApp
     * @return BusReg
     */
    public function setIsTxcApp($isTxcApp)
    {
        $this->isTxcApp = $isTxcApp;

        return $this;
    }

    /**
     * Get the is txc app
     *
     * @return string
     */
    public function getIsTxcApp()
    {
        return $this->isTxcApp;
    }

    /**
     * Set the la short note
     *
     * @param string $laShortNote
     * @return BusReg
     */
    public function setLaShortNote($laShortNote)
    {
        $this->laShortNote = $laShortNote;

        return $this;
    }

    /**
     * Get the la short note
     *
     * @return string
     */
    public function getLaShortNote()
    {
        return $this->laShortNote;
    }

    /**
     * Set the local authority
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $localAuthoritys
     * @return BusReg
     */
    public function setLocalAuthoritys($localAuthoritys)
    {
        $this->localAuthoritys = $localAuthoritys;

        return $this;
    }

    /**
     * Get the local authoritys
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLocalAuthoritys()
    {
        return $this->localAuthoritys;
    }

    /**
     * Add a local authoritys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $localAuthoritys
     * @return BusReg
     */
    public function addLocalAuthoritys($localAuthoritys)
    {
        if ($localAuthoritys instanceof ArrayCollection) {
            $this->localAuthoritys = new ArrayCollection(
                array_merge(
                    $this->localAuthoritys->toArray(),
                    $localAuthoritys->toArray()
                )
            );
        } elseif (!$this->localAuthoritys->contains($localAuthoritys)) {
            $this->localAuthoritys->add($localAuthoritys);
        }

        return $this;
    }

    /**
     * Remove a local authoritys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $localAuthoritys
     * @return BusReg
     */
    public function removeLocalAuthoritys($localAuthoritys)
    {
        if ($this->localAuthoritys->contains($localAuthoritys)) {
            $this->localAuthoritys->removeElement($localAuthoritys);
        }

        return $this;
    }

    /**
     * Set the manoeuvre detail
     *
     * @param string $manoeuvreDetail
     * @return BusReg
     */
    public function setManoeuvreDetail($manoeuvreDetail)
    {
        $this->manoeuvreDetail = $manoeuvreDetail;

        return $this;
    }

    /**
     * Get the manoeuvre detail
     *
     * @return string
     */
    public function getManoeuvreDetail()
    {
        return $this->manoeuvreDetail;
    }

    /**
     * Set the map supplied
     *
     * @param string $mapSupplied
     * @return BusReg
     */
    public function setMapSupplied($mapSupplied)
    {
        $this->mapSupplied = $mapSupplied;

        return $this;
    }

    /**
     * Get the map supplied
     *
     * @return string
     */
    public function getMapSupplied()
    {
        return $this->mapSupplied;
    }

    /**
     * Set the need new stop
     *
     * @param string $needNewStop
     * @return BusReg
     */
    public function setNeedNewStop($needNewStop)
    {
        $this->needNewStop = $needNewStop;

        return $this;
    }

    /**
     * Get the need new stop
     *
     * @return string
     */
    public function getNeedNewStop()
    {
        return $this->needNewStop;
    }

    /**
     * Set the new stop detail
     *
     * @param string $newStopDetail
     * @return BusReg
     */
    public function setNewStopDetail($newStopDetail)
    {
        $this->newStopDetail = $newStopDetail;

        return $this;
    }

    /**
     * Get the new stop detail
     *
     * @return string
     */
    public function getNewStopDetail()
    {
        return $this->newStopDetail;
    }

    /**
     * Set the not fixed stop detail
     *
     * @param string $notFixedStopDetail
     * @return BusReg
     */
    public function setNotFixedStopDetail($notFixedStopDetail)
    {
        $this->notFixedStopDetail = $notFixedStopDetail;

        return $this;
    }

    /**
     * Get the not fixed stop detail
     *
     * @return string
     */
    public function getNotFixedStopDetail()
    {
        return $this->notFixedStopDetail;
    }

    /**
     * Set the op notified la pte
     *
     * @param string $opNotifiedLaPte
     * @return BusReg
     */
    public function setOpNotifiedLaPte($opNotifiedLaPte)
    {
        $this->opNotifiedLaPte = $opNotifiedLaPte;

        return $this;
    }

    /**
     * Get the op notified la pte
     *
     * @return string
     */
    public function getOpNotifiedLaPte()
    {
        return $this->opNotifiedLaPte;
    }

    /**
     * Set the organisation email
     *
     * @param string $organisationEmail
     * @return BusReg
     */
    public function setOrganisationEmail($organisationEmail)
    {
        $this->organisationEmail = $organisationEmail;

        return $this;
    }

    /**
     * Get the organisation email
     *
     * @return string
     */
    public function getOrganisationEmail()
    {
        return $this->organisationEmail;
    }

    /**
     * Set the other details
     *
     * @param string $otherDetails
     * @return BusReg
     */
    public function setOtherDetails($otherDetails)
    {
        $this->otherDetails = $otherDetails;

        return $this;
    }

    /**
     * Get the other details
     *
     * @return string
     */
    public function getOtherDetails()
    {
        return $this->otherDetails;
    }

    /**
     * Set the parent
     *
     * @param \Olcs\Db\Entity\BusReg $parent
     * @return BusReg
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the quality contract details
     *
     * @param string $qualityContractDetails
     * @return BusReg
     */
    public function setQualityContractDetails($qualityContractDetails)
    {
        $this->qualityContractDetails = $qualityContractDetails;

        return $this;
    }

    /**
     * Get the quality contract details
     *
     * @return string
     */
    public function getQualityContractDetails()
    {
        return $this->qualityContractDetails;
    }

    /**
     * Set the quality partnership details
     *
     * @param string $qualityPartnershipDetails
     * @return BusReg
     */
    public function setQualityPartnershipDetails($qualityPartnershipDetails)
    {
        $this->qualityPartnershipDetails = $qualityPartnershipDetails;

        return $this;
    }

    /**
     * Get the quality partnership details
     *
     * @return string
     */
    public function getQualityPartnershipDetails()
    {
        return $this->qualityPartnershipDetails;
    }

    /**
     * Set the quality partnership facilities used
     *
     * @param string $qualityPartnershipFacilitiesUsed
     * @return BusReg
     */
    public function setQualityPartnershipFacilitiesUsed($qualityPartnershipFacilitiesUsed)
    {
        $this->qualityPartnershipFacilitiesUsed = $qualityPartnershipFacilitiesUsed;

        return $this;
    }

    /**
     * Get the quality partnership facilities used
     *
     * @return string
     */
    public function getQualityPartnershipFacilitiesUsed()
    {
        return $this->qualityPartnershipFacilitiesUsed;
    }

    /**
     * Set the reason cancelled
     *
     * @param string $reasonCancelled
     * @return BusReg
     */
    public function setReasonCancelled($reasonCancelled)
    {
        $this->reasonCancelled = $reasonCancelled;

        return $this;
    }

    /**
     * Get the reason cancelled
     *
     * @return string
     */
    public function getReasonCancelled()
    {
        return $this->reasonCancelled;
    }

    /**
     * Set the reason refused
     *
     * @param string $reasonRefused
     * @return BusReg
     */
    public function setReasonRefused($reasonRefused)
    {
        $this->reasonRefused = $reasonRefused;

        return $this;
    }

    /**
     * Get the reason refused
     *
     * @return string
     */
    public function getReasonRefused()
    {
        return $this->reasonRefused;
    }

    /**
     * Set the reason sn refused
     *
     * @param string $reasonSnRefused
     * @return BusReg
     */
    public function setReasonSnRefused($reasonSnRefused)
    {
        $this->reasonSnRefused = $reasonSnRefused;

        return $this;
    }

    /**
     * Get the reason sn refused
     *
     * @return string
     */
    public function getReasonSnRefused()
    {
        return $this->reasonSnRefused;
    }

    /**
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return BusReg
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get the received date
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    /**
     * Set the reg no
     *
     * @param string $regNo
     * @return BusReg
     */
    public function setRegNo($regNo)
    {
        $this->regNo = $regNo;

        return $this;
    }

    /**
     * Get the reg no
     *
     * @return string
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * Set the revert status
     *
     * @param \Olcs\Db\Entity\RefData $revertStatus
     * @return BusReg
     */
    public function setRevertStatus($revertStatus)
    {
        $this->revertStatus = $revertStatus;

        return $this;
    }

    /**
     * Get the revert status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRevertStatus()
    {
        return $this->revertStatus;
    }

    /**
     * Set the route description
     *
     * @param string $routeDescription
     * @return BusReg
     */
    public function setRouteDescription($routeDescription)
    {
        $this->routeDescription = $routeDescription;

        return $this;
    }

    /**
     * Get the route description
     *
     * @return string
     */
    public function getRouteDescription()
    {
        return $this->routeDescription;
    }

    /**
     * Set the route no
     *
     * @param int $routeNo
     * @return BusReg
     */
    public function setRouteNo($routeNo)
    {
        $this->routeNo = $routeNo;

        return $this;
    }

    /**
     * Get the route no
     *
     * @return int
     */
    public function getRouteNo()
    {
        return $this->routeNo;
    }

    /**
     * Set the short notice refused
     *
     * @param string $shortNoticeRefused
     * @return BusReg
     */
    public function setShortNoticeRefused($shortNoticeRefused)
    {
        $this->shortNoticeRefused = $shortNoticeRefused;

        return $this;
    }

    /**
     * Get the short notice refused
     *
     * @return string
     */
    public function getShortNoticeRefused()
    {
        return $this->shortNoticeRefused;
    }

    /**
     * Set the start point
     *
     * @param string $startPoint
     * @return BusReg
     */
    public function setStartPoint($startPoint)
    {
        $this->startPoint = $startPoint;

        return $this;
    }

    /**
     * Get the start point
     *
     * @return string
     */
    public function getStartPoint()
    {
        return $this->startPoint;
    }

    /**
     * Set the stopping arrangements
     *
     * @param string $stoppingArrangements
     * @return BusReg
     */
    public function setStoppingArrangements($stoppingArrangements)
    {
        $this->stoppingArrangements = $stoppingArrangements;

        return $this;
    }

    /**
     * Get the stopping arrangements
     *
     * @return string
     */
    public function getStoppingArrangements()
    {
        return $this->stoppingArrangements;
    }

    /**
     * Set the subsidised
     *
     * @param \Olcs\Db\Entity\RefData $subsidised
     * @return BusReg
     */
    public function setSubsidised($subsidised)
    {
        $this->subsidised = $subsidised;

        return $this;
    }

    /**
     * Get the subsidised
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getSubsidised()
    {
        return $this->subsidised;
    }

    /**
     * Set the subsidy detail
     *
     * @param string $subsidyDetail
     * @return BusReg
     */
    public function setSubsidyDetail($subsidyDetail)
    {
        $this->subsidyDetail = $subsidyDetail;

        return $this;
    }

    /**
     * Get the subsidy detail
     *
     * @return string
     */
    public function getSubsidyDetail()
    {
        return $this->subsidyDetail;
    }

    /**
     * Set the timetable acceptable
     *
     * @param string $timetableAcceptable
     * @return BusReg
     */
    public function setTimetableAcceptable($timetableAcceptable)
    {
        $this->timetableAcceptable = $timetableAcceptable;

        return $this;
    }

    /**
     * Get the timetable acceptable
     *
     * @return string
     */
    public function getTimetableAcceptable()
    {
        return $this->timetableAcceptable;
    }

    /**
     * Set the traffic area
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $trafficAreas
     * @return BusReg
     */
    public function setTrafficAreas($trafficAreas)
    {
        $this->trafficAreas = $trafficAreas;

        return $this;
    }

    /**
     * Get the traffic areas
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTrafficAreas()
    {
        return $this->trafficAreas;
    }

    /**
     * Add a traffic areas
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $trafficAreas
     * @return BusReg
     */
    public function addTrafficAreas($trafficAreas)
    {
        if ($trafficAreas instanceof ArrayCollection) {
            $this->trafficAreas = new ArrayCollection(
                array_merge(
                    $this->trafficAreas->toArray(),
                    $trafficAreas->toArray()
                )
            );
        } elseif (!$this->trafficAreas->contains($trafficAreas)) {
            $this->trafficAreas->add($trafficAreas);
        }

        return $this;
    }

    /**
     * Remove a traffic areas
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $trafficAreas
     * @return BusReg
     */
    public function removeTrafficAreas($trafficAreas)
    {
        if ($this->trafficAreas->contains($trafficAreas)) {
            $this->trafficAreas->removeElement($trafficAreas);
        }

        return $this;
    }

    /**
     * Set the trc condition checked
     *
     * @param string $trcConditionChecked
     * @return BusReg
     */
    public function setTrcConditionChecked($trcConditionChecked)
    {
        $this->trcConditionChecked = $trcConditionChecked;

        return $this;
    }

    /**
     * Get the trc condition checked
     *
     * @return string
     */
    public function getTrcConditionChecked()
    {
        return $this->trcConditionChecked;
    }

    /**
     * Set the trc notes
     *
     * @param string $trcNotes
     * @return BusReg
     */
    public function setTrcNotes($trcNotes)
    {
        $this->trcNotes = $trcNotes;

        return $this;
    }

    /**
     * Get the trc notes
     *
     * @return string
     */
    public function getTrcNotes()
    {
        return $this->trcNotes;
    }

    /**
     * Set the txc app type
     *
     * @param string $txcAppType
     * @return BusReg
     */
    public function setTxcAppType($txcAppType)
    {
        $this->txcAppType = $txcAppType;

        return $this;
    }

    /**
     * Get the txc app type
     *
     * @return string
     */
    public function getTxcAppType()
    {
        return $this->txcAppType;
    }

    /**
     * Set the use all stops
     *
     * @param string $useAllStops
     * @return BusReg
     */
    public function setUseAllStops($useAllStops)
    {
        $this->useAllStops = $useAllStops;

        return $this;
    }

    /**
     * Get the use all stops
     *
     * @return string
     */
    public function getUseAllStops()
    {
        return $this->useAllStops;
    }

    /**
     * Set the variation no
     *
     * @param int $variationNo
     * @return BusReg
     */
    public function setVariationNo($variationNo)
    {
        $this->variationNo = $variationNo;

        return $this;
    }

    /**
     * Get the variation no
     *
     * @return int
     */
    public function getVariationNo()
    {
        return $this->variationNo;
    }

    /**
     * Set the variation reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $variationReasons
     * @return BusReg
     */
    public function setVariationReasons($variationReasons)
    {
        $this->variationReasons = $variationReasons;

        return $this;
    }

    /**
     * Get the variation reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVariationReasons()
    {
        return $this->variationReasons;
    }

    /**
     * Add a variation reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $variationReasons
     * @return BusReg
     */
    public function addVariationReasons($variationReasons)
    {
        if ($variationReasons instanceof ArrayCollection) {
            $this->variationReasons = new ArrayCollection(
                array_merge(
                    $this->variationReasons->toArray(),
                    $variationReasons->toArray()
                )
            );
        } elseif (!$this->variationReasons->contains($variationReasons)) {
            $this->variationReasons->add($variationReasons);
        }

        return $this;
    }

    /**
     * Remove a variation reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $variationReasons
     * @return BusReg
     */
    public function removeVariationReasons($variationReasons)
    {
        if ($this->variationReasons->contains($variationReasons)) {
            $this->variationReasons->removeElement($variationReasons);
        }

        return $this;
    }

    /**
     * Set the via
     *
     * @param string $via
     * @return BusReg
     */
    public function setVia($via)
    {
        $this->via = $via;

        return $this;
    }

    /**
     * Get the via
     *
     * @return string
     */
    public function getVia()
    {
        return $this->via;
    }

    /**
     * Set the other service
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $otherServices
     * @return BusReg
     */
    public function setOtherServices($otherServices)
    {
        $this->otherServices = $otherServices;

        return $this;
    }

    /**
     * Get the other services
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOtherServices()
    {
        return $this->otherServices;
    }

    /**
     * Add a other services
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $otherServices
     * @return BusReg
     */
    public function addOtherServices($otherServices)
    {
        if ($otherServices instanceof ArrayCollection) {
            $this->otherServices = new ArrayCollection(
                array_merge(
                    $this->otherServices->toArray(),
                    $otherServices->toArray()
                )
            );
        } elseif (!$this->otherServices->contains($otherServices)) {
            $this->otherServices->add($otherServices);
        }

        return $this;
    }

    /**
     * Remove a other services
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $otherServices
     * @return BusReg
     */
    public function removeOtherServices($otherServices)
    {
        if ($this->otherServices->contains($otherServices)) {
            $this->otherServices->removeElement($otherServices);
        }

        return $this;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return BusReg
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
     * @return BusReg
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
     * @return BusReg
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }
}
