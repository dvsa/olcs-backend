<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * BusReg Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_reg",
 *    indexes={
 *        @ORM\Index(name="fk_bus_reg_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_bus_reg_bus_notice_period1_idx", columns={"bus_notice_period_id"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data1_idx", columns={"subsidised"}),
 *        @ORM\Index(name="fk_bus_reg_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_bus_reg_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_reg_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_bus_reg_ref_data2_idx", columns={"withdrawn_reason"})
 *    }
 * )
 */
class BusReg implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\WithdrawnReasonManyToOne,
        Traits\LicenceManyToOne,
        Traits\CreatedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\ServiceNo70Field,
        Traits\EffectiveDateField,
        Traits\EndDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Bus notice period
     *
     * @var \Olcs\Db\Entity\BusNoticePeriod
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusNoticePeriod")
     * @ORM\JoinColumn(name="bus_notice_period_id", referencedColumnName="id")
     */
    protected $busNoticePeriod;

    /**
     * Subsidised
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="subsidised", referencedColumnName="id")
     */
    protected $subsidised;

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
     * Bus service type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\BusServiceType", mappedBy="busRegs")
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
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="received_date", nullable=true)
     */
    protected $receivedDate;

    /**
     * Is short notice
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_short_notice", nullable=false)
     */
    protected $isShortNotice = 0;

    /**
     * Use all stops
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="use_all_stops", nullable=false)
     */
    protected $useAllStops = 0;

    /**
     * Has manoeuvre
     *
     * @var unknown
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
     * @var unknown
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
     * @var unknown
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="timetable_acceptable", nullable=false)
     */
    protected $timetableAcceptable = 0;

    /**
     * Map supplied
     *
     * @var unknown
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="copied_to_la_pte", nullable=false)
     */
    protected $copiedToLaPte = 0;

    /**
     * La short note
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="la_short_note", nullable=false)
     */
    protected $laShortNote = 0;

    /**
     * Application signed
     *
     * @var unknown
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
     * @var unknown
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
     * @var unknown
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
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status", length=20, nullable=false)
     */
    protected $status;

    /**
     * Revert status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="revert_status", length=20, nullable=true)
     */
    protected $revertStatus;

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
     * @var unknown
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="short_notice_refused", nullable=false)
     */
    protected $shortNoticeRefused = 0;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->variationReasons = new ArrayCollection();
        $this->busServiceTypes = new ArrayCollection();
    }

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
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
            $this->variationReasons->remove($variationReasons);
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
            $this->busServiceTypes->remove($busServiceTypes);
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
     * Set the is short notice
     *
     * @param unknown $isShortNotice
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
     * @return unknown
     */
    public function getIsShortNotice()
    {
        return $this->isShortNotice;
    }


    /**
     * Set the use all stops
     *
     * @param unknown $useAllStops
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
     * @return unknown
     */
    public function getUseAllStops()
    {
        return $this->useAllStops;
    }


    /**
     * Set the has manoeuvre
     *
     * @param unknown $hasManoeuvre
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
     * @return unknown
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
     * @param unknown $needNewStop
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
     * @return unknown
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
     * @param unknown $hasNotFixedStop
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
     * @return unknown
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
     * @param unknown $timetableAcceptable
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
     * @return unknown
     */
    public function getTimetableAcceptable()
    {
        return $this->timetableAcceptable;
    }


    /**
     * Set the map supplied
     *
     * @param unknown $mapSupplied
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
     * @return unknown
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
     * @param unknown $copiedToLaPte
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
     * @return unknown
     */
    public function getCopiedToLaPte()
    {
        return $this->copiedToLaPte;
    }


    /**
     * Set the la short note
     *
     * @param unknown $laShortNote
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
     * @return unknown
     */
    public function getLaShortNote()
    {
        return $this->laShortNote;
    }


    /**
     * Set the application signed
     *
     * @param unknown $applicationSigned
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
     * @return unknown
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
     * @param unknown $opNotifiedLaPte
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
     * @return unknown
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
     * @param unknown $trcConditionChecked
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
     * @return unknown
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
     * Set the status
     *
     * @param string $status
     * @return BusReg
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Set the revert status
     *
     * @param string $revertStatus
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
     * @return string
     */
    public function getRevertStatus()
    {
        return $this->revertStatus;
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
     * @param unknown $isTxcApp
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
     * @return unknown
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
     * @param unknown $shortNoticeRefused
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
     * @return unknown
     */
    public function getShortNoticeRefused()
    {
        return $this->shortNoticeRefused;
    }

}
