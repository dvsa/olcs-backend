<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BusReg Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_reg",
 *    indexes={
 *        @ORM\Index(name="fk_bus_reg_licence1_idx", 
 *            columns={"licence_id"}),
 *        @ORM\Index(name="fk_bus_reg_bus_notice_period1_idx", 
 *            columns={"bus_notice_period_id"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data1_idx", 
 *            columns={"subsidised"}),
 *        @ORM\Index(name="fk_bus_reg_operating_centre1_idx", 
 *            columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_bus_reg_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_reg_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data2_idx", 
 *            columns={"withdrawn_reason"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data3_idx", 
 *            columns={"status"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data4_idx", 
 *            columns={"revert_status"})
 *    }
 * )
 */
class BusReg implements Interfaces\EntityInterface
{

    /**
     * Revert status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="revert_status", referencedColumnName="id", nullable=false)
     */
    protected $revertStatus;

    /**
     * Bus notice period
     *
     * @var \Olcs\Db\Entity\BusNoticePeriod
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusNoticePeriod", fetch="LAZY")
     * @ORM\JoinColumn(name="bus_notice_period_id", referencedColumnName="id", nullable=false)
     */
    protected $busNoticePeriod;

    /**
     * Subsidised
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="subsidised", referencedColumnName="id", nullable=false)
     */
    protected $subsidised;

    /**
     * Variation reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\VariationReason", inversedBy="busRegs", fetch="LAZY")
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
     * Bus service type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\BusServiceType", inversedBy="busRegs", fetch="LAZY")
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
     * Route no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="route_no", nullable=false)
     */
    protected $routeNo;

    /**
     * Reg no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reg_no", length=70, nullable=false)
     */
    protected $regNo;

    /**
     * Service no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="service_no", length=70, nullable=true)
     */
    protected $serviceNo;

    /**
     * Start point
     *
     * @var string
     *
     * @ORM\Column(type="string", name="start_point", length=100, nullable=true)
     */
    protected $startPoint;

    /**
     * Finish point
     *
     * @var string
     *
     * @ORM\Column(type="string", name="finish_point", length=100, nullable=true)
     */
    protected $finishPoint;

    /**
     * Via
     *
     * @var string
     *
     * @ORM\Column(type="string", name="via", length=255, nullable=true)
     */
    protected $via;

    /**
     * Other details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="other_details", length=800, nullable=true)
     */
    protected $otherDetails;

    /**
     * Is short notice
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_short_notice", nullable=false)
     */
    protected $isShortNotice = 0;

    /**
     * Use all stops
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="use_all_stops", nullable=false)
     */
    protected $useAllStops = 0;

    /**
     * Has manoeuvre
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="has_manoeuvre", nullable=false)
     */
    protected $hasManoeuvre = 0;

    /**
     * Manoeuvre detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="manoeuvre_detail", length=255, nullable=true)
     */
    protected $manoeuvreDetail;

    /**
     * Need new stop
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="need_new_stop", nullable=false)
     */
    protected $needNewStop = 0;

    /**
     * New stop detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="new_stop_detail", length=255, nullable=true)
     */
    protected $newStopDetail;

    /**
     * Has not fixed stop
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="has_not_fixed_stop", nullable=false)
     */
    protected $hasNotFixedStop = 0;

    /**
     * Not fixed stop detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="not_fixed_stop_detail", length=255, nullable=true)
     */
    protected $notFixedStopDetail;

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
     * @ORM\Column(type="yesno", name="timetable_acceptable", nullable=false)
     */
    protected $timetableAcceptable = 0;

    /**
     * Map supplied
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="map_supplied", nullable=false)
     */
    protected $mapSupplied = 0;

    /**
     * Route description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="route_description", length=1000, nullable=true)
     */
    protected $routeDescription;

    /**
     * Copied to la pte
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="copied_to_la_pte", nullable=false)
     */
    protected $copiedToLaPte = 0;

    /**
     * La short note
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="la_short_note", nullable=false)
     */
    protected $laShortNote = 0;

    /**
     * Application signed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="application_signed", nullable=false)
     */
    protected $applicationSigned = 0;

    /**
     * Completed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="completed_date", nullable=true)
     */
    protected $completedDate;

    /**
     * Route seq
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="route_seq", nullable=false)
     */
    protected $routeSeq = 0;

    /**
     * Op notified la pte
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="op_notified_la_pte", nullable=false)
     */
    protected $opNotifiedLaPte = 0;

    /**
     * Stopping arrangements
     *
     * @var string
     *
     * @ORM\Column(type="string", name="stopping_arrangements", length=800, nullable=true)
     */
    protected $stoppingArrangements;

    /**
     * Trc condition checked
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="trc_condition_checked", nullable=false)
     */
    protected $trcConditionChecked = 0;

    /**
     * Trc notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="trc_notes", length=255, nullable=true)
     */
    protected $trcNotes;

    /**
     * Organisation email
     *
     * @var string
     *
     * @ORM\Column(type="string", name="organisation_email", length=255, nullable=true)
     */
    protected $organisationEmail;

    /**
     * Is txc app
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_txc_app", nullable=false)
     */
    protected $isTxcApp = 0;

    /**
     * Txc app type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_app_type", length=20, nullable=true)
     */
    protected $txcAppType;

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
     * Short notice refused
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="short_notice_refused", nullable=false)
     */
    protected $shortNoticeRefused = 0;

    /**
     * Is quality partnership
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_quality_partnership", nullable=false)
     */
    protected $isQualityPartnership = 0;

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
     * @ORM\Column(type="yesno", name="quality_partnership_facilities_used", nullable=false)
     */
    protected $qualityPartnershipFacilitiesUsed = 0;

    /**
     * Is quality contract
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_quality_contract", nullable=false)
     */
    protected $isQualityContract = 0;

    /**
     * Quality contract details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="quality_contract_details", length=4000, nullable=true)
     */
    protected $qualityContractDetails;

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
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

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
     * Status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    protected $status;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Withdrawn reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdrawn_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawnReason;

    /**
     * Operating centre
     *
     * @var \Olcs\Db\Entity\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\OperatingCentre", fetch="LAZY")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=true)
     */
    protected $operatingCentre;

    /**
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="received_date", nullable=false)
     */
    protected $receivedDate;

    /**
     * Effective date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_date", nullable=true)
     */
    protected $effectiveDate;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_date", nullable=true)
     */
    protected $endDate;

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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->variationReasons = new ArrayCollection();
        $this->busServiceTypes = new ArrayCollection();
        $this->otherServices = new ArrayCollection();
        $this->documents = new ArrayCollection();
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
     * Set the service no
     *
     * @param string $serviceNo
     * @return BusReg
     */
    public function setServiceNo($serviceNo)
    {
        $this->serviceNo = $serviceNo;

        return $this;
    }

    /**
     * Get the service no
     *
     * @return string
     */
    public function getServiceNo()
    {
        return $this->serviceNo;
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
     * Set the completed date
     *
     * @param \DateTime $completedDate
     * @return BusReg
     */
    public function setCompletedDate($completedDate)
    {
        $this->completedDate = $completedDate;

        return $this;
    }

    /**
     * Get the completed date
     *
     * @return \DateTime
     */
    public function getCompletedDate()
    {
        return $this->completedDate;
    }

    /**
     * Set the route seq
     *
     * @param int $routeSeq
     * @return BusReg
     */
    public function setRouteSeq($routeSeq)
    {
        $this->routeSeq = $routeSeq;

        return $this;
    }

    /**
     * Get the route seq
     *
     * @return int
     */
    public function getRouteSeq()
    {
        return $this->routeSeq;
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
     * Set the status
     *
     * @param \Olcs\Db\Entity\RefData $status
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the withdrawn reason
     *
     * @param \Olcs\Db\Entity\RefData $withdrawnReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
    }

    /**
     * Set the operating centre
     *
     * @param \Olcs\Db\Entity\OperatingCentre $operatingCentre
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOperatingCentre($operatingCentre)
    {
        $this->operatingCentre = $operatingCentre;

        return $this;
    }

    /**
     * Get the operating centre
     *
     * @return \Olcs\Db\Entity\OperatingCentre
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }

    /**
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the effective date
     *
     * @param \DateTime $effectiveDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get the effective date
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set the end date
     *
     * @param \DateTime $endDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
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
