<?php

namespace Dvsa\Olcs\Api\Entity\Bus;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as BusShortNoticeEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService as BusRegOtherServiceEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;

/**
 * BusReg Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="bus_reg",
 *    indexes={
 *        @ORM\Index(name="ix_bus_reg_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_bus_reg_bus_notice_period_id", columns={"bus_notice_period_id"}),
 *        @ORM\Index(name="ix_bus_reg_subsidised", columns={"subsidised"}),
 *        @ORM\Index(name="ix_bus_reg_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_bus_reg_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_bus_reg_withdrawn_reason", columns={"withdrawn_reason"}),
 *        @ORM\Index(name="ix_bus_reg_status", columns={"status"}),
 *        @ORM\Index(name="ix_bus_reg_revert_status", columns={"revert_status"}),
 *        @ORM\Index(name="ix_bus_reg_reg_no", columns={"reg_no"}),
 *        @ORM\Index(name="fk_bus_reg_parent_id_bus_reg_id", columns={"parent_id"}),
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_bus_reg_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class BusReg extends AbstractBusReg implements ContextProviderInterface, OrganisationProviderInterface
{
    const STATUS_NEW = 'breg_s_new';
    const STATUS_VAR = 'breg_s_var';
    const STATUS_CANCEL = 'breg_s_cancellation';
    const STATUS_ADMIN = 'breg_s_admin';
    const STATUS_REGISTERED = 'breg_s_registered';
    const STATUS_REFUSED = 'breg_s_refused';
    const STATUS_WITHDRAWN = 'breg_s_withdrawn';
    const STATUS_CNS = 'breg_s_cns';
    const STATUS_CANCELLED = 'breg_s_cancelled';

    const SUBSIDY_NO = 'bs_no';

    const FORBIDDEN_ERROR = 'This bus reg can\'t be edited. It must be the latest variation, and not from EBSR';

    public static $ebsrExistingRecordExcluded = [
        self::STATUS_REFUSED,
        self::STATUS_WITHDRAWN
    ];

    /**
     * @var array
     */
    private static $grantStatusMap = [
        self::STATUS_NEW => self::STATUS_REGISTERED,
        self::STATUS_VAR => self::STATUS_REGISTERED,
        self::STATUS_CANCEL => self::STATUS_CANCELLED,
    ];

    /**
     * @var array
     */
    private static $defaultAll = [
        // Reason for action text fields should all be empty
        'reasonSnRefused' => '',
        'reasonCancelled' => '',
        'reasonRefused' => '',
        // Withdrawn reason can be null; its here to override any value set in a variation/cancellation
        'withdrawnReason' => null,
        // At time of creation, we don't know if its short notice or not. Default to no.
        'isShortNotice' => 'N',
        // This is a new application/variation so hasn't been refused by short notice (yet)
        'shortNoticeRefused' => 'N',
        // Checks before granting should all default to no
        'copiedToLaPte' => 'N',
        'laShortNote' => 'N',
        'applicationSigned' => 'N',
        'opNotifiedLaPte' => 'N',
        // Trc conditions should also default to no/empty
        'trcConditionChecked' => 'N',
        'trcNotes' => '',
        // Timetable conditions should default to no
        'timetableAcceptable' => 'N',
        'mapSupplied' => 'N',
        // (Re)set dates to null
        'receivedDate' => null,
        'effectiveDate' => null,
        'endDate' => null,
        // These will be set to yes explicitly by the TXC processor, default it to no for the internal app
        'isTxcApp' => 'N',
        'ebsrRefresh' => 'N'
    ];

    /**
     * Create new BusReg
     *
     * @param LicenceEntity         $licence         Licence
     * @param RefData               $status          Status
     * @param RefData               $revertStatus    Revert status
     * @param RefData               $subsidised      Subsidised
     * @param BusNoticePeriodEntity $busNoticePeriod Bus notice period
     *
     * @return BusReg
     */
    public static function createNew(
        LicenceEntity $licence,
        RefData $status,
        RefData $revertStatus,
        RefData $subsidised,
        BusNoticePeriodEntity $busNoticePeriod
    ) {
        // get default data
        $data = array_merge(
            self::$defaultAll,
            [
                'variationNo' => 0,
                 // Should this be moved to all? and the details field wiped?
                'needNewStop' => 'N',
                'hasManoeuvre' => 'N',
                'hasNotFixedStop' => 'N',
                // Reg number is generated based upon the licence and route number. empty by default.
                'regNo' => '',
                'routeNo' => 0,
                // Some discussion over what value of this should be John Spellman has now confirmed it
                'useAllStops' => 'N',
                'isQualityContract' => 'N',
                'isQualityPartnership' => 'N',
                'qualityPartnershipFacilitiesUsed' => 'N'
            ]
        );

        // create bus reg with defaults
        $busReg = new self();
        $busReg->fromData($data);

        // set reference data
        $busReg->setLicence($licence);
        $busReg->setStatus($status);
        $busReg->setRevertStatus($revertStatus);
        $busReg->setSubsidised($subsidised);
        $busReg->setBusNoticePeriod($busNoticePeriod);

        // set default short notice
        $busShortNotice = new BusShortNoticeEntity();
        $busShortNotice->setBusReg($busReg);
        $busReg->setShortNotice($busShortNotice);

        // get the most recent Route No for the licence and increment it
        $newRouteNo = (int)$licence->getLatestBusRouteNo() + 1;
        $busReg->setRouteNo($newRouteNo);

        // set Reg No
        $regNo = $licence->getLicNo().'/'.$newRouteNo;
        $busReg->setRegNo($regNo);

        return $busReg;
    }

    /**
     * Returns whether the bus reg is an application for cancellation
     *
     * @return bool
     */
    public function isCancellation()
    {
        return $this->status->getId() === self::STATUS_CANCEL;
    }

    /**
     * Returns whether the bus reg is a registered bus route
     *
     * @return bool
     */
    public function isRegistered()
    {
        return $this->status->getId() === self::STATUS_REGISTERED;
    }

    /**
     * Returns whether a variation can be created
     *
     * @return bool
     */
    public function canCreateVariation()
    {
        return $this->isRegistered();
    }

    /**
     * Create BusReg variation
     *
     * @param RefData $status       Status
     * @param RefData $revertStatus Revert status
     *
     * @throws ForbiddenException
     * @return BusReg
     */
    public function createVariation(
        RefData $status,
        RefData $revertStatus
    ) {
        if (!$this->canCreateVariation()) {
            throw new ForbiddenException('Can only create a variation/cancellation against a registered bus route');
        }

        // create bus reg based on the previous record
        $busReg = clone $this;

        $data = array_merge(
            // override columns which need different defaults for a variation
            self::$defaultAll,
            [
                // unset database metadata
                'id' => null,
                'version' => null,
                'createdBy' => null,
                'lastModifiedBy' => null,
                'createdOn' => null,
                'lastModifiedOn' => null,
                'olbsKey' => null,
                // new variation reasons will be required for a new variation
                'variationReasons' => null,
            ]
        );
        $busReg->fromData($data);

        // set parent
        $busReg->setParent($this);

        // set reference data
        $busReg->setStatus($status);
        $busReg->setStatusChangeDate(new \DateTime);
        $busReg->setRevertStatus($revertStatus);

        // get the latest variation no for the reg no and increment it
        $newVariationNo
            = (int)$busReg->getLicence()->getLatestBusVariation($busReg->getRegNo(), [])->getVariationNo() + 1;
        $busReg->setVariationNo($newVariationNo);

        // set default short notice
        $busShortNotice = new BusShortNoticeEntity();
        $busShortNotice->setBusReg($busReg);
        $busReg->setShortNotice($busShortNotice);

        // make a copy of otherServices
        $otherServices = new ArrayCollection();

        foreach ($this->getOtherServices() as $otherService) {
            $newOtherService = clone $otherService;

            // unset database metadata
            $newOtherService->setId(null);
            $newOtherService->setVersion(null);
            $newOtherService->setCreatedBy(null);
            $newOtherService->setLastModifiedBy(null);
            $newOtherService->setCreatedOn(null);
            $newOtherService->setLastModifiedOn(null);
            $newOtherService->setOlbsKey(null);
            $newOtherService->setBusReg($busReg);

            $otherServices->add($newOtherService);
        }
        $busReg->setOtherServices($otherServices);

        return $busReg;
    }

    /**
     * Populate properties from data
     *
     * @param array $data Data
     *
     * @return void
     */
    public function fromData($data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucwords($key);

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * A bus reg may only be edited if it's the latest variation, and the record didn't come from EBSR
     *
     * @return bool
     * @throws ForbiddenException
     */
    public function canEdit()
    {
        if (!$this->isFromEbsr() && $this->isLatestVariation()) {
            return true;
        }

        throw new ForbiddenException('No permission to edit this record');
    }

    /**
     * A bus reg may only be deleted if it's the latest variation
     *
     * @return bool
     * @throws ForbiddenException
     */
    public function canDelete()
    {
        if ($this->isLatestVariation()) {
            return true;
        }

        throw new ForbiddenException('Only the latest variation may be deleted');
    }

    /**
     * A decision about a bus reg can be made only if it's the latest variation
     *
     * @return bool
     * @throws ForbiddenException
     */
    public function canMakeDecision()
    {
        if ($this->isLatestVariation()) {
            return true;
        }

        throw new ForbiddenException('Decision can be made on the latest variation only');
    }

    /**
     * Returns whether the variation is the latest one
     *
     * @return bool
     */
    public function isLatestVariation()
    {
        $latestBusVariation = $this->getLicence()->getLatestBusVariation($this->getRegNo());

        if (empty($latestBusVariation)) {
            return true;
        }
        return $this->getId() === $latestBusVariation->getId();
    }

    /**
     * Returns whether the record is from EBSR
     *
     * @return bool
     */
    public function isFromEbsr()
    {
        return ($this->isTxcApp === 'Y' ? true : false);
    }

    /**
     * Returns whether the record uses Scottish rules
     *
     * @return bool
     */
    public function isScottishRules()
    {
        return $this->busNoticePeriod->isScottishRules();
    }

    /**
     * Returns whether a bus reg is read only based on status and variation.
     *
     * @note Ebsr is not checked (EBSR pages are often read only on the front end) - to check Ebsr use isFromEbsr()
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return !$this->isLatestVariation() ||
        in_array($this->status->getId(), [self::STATUS_REGISTERED, self::STATUS_CANCELLED]);
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedValues()
    {
        return [
            'licence' => null,
            'parent' => null,
            'isLatestVariation' => $this->isLatestVariation(),
            'isReadOnly' => $this->isReadOnly(),
            'isScottishRules' => $this->isScottishRules(),
            'isFromEbsr' => $this->isFromEbsr(),
            'shortNotice' => null
        ];
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'isLatestVariation' => $this->isLatestVariation(),
            'isReadOnly' => $this->isReadOnly(),
            'isScottishRules' => $this->isScottishRules(),
            'isFromEbsr' => $this->isFromEbsr()
        ];
    }

    /**
     * Update stops
     *
     * @param string  $useAllStops        Use all stops
     * @param string  $hasManoeuvre       Has manoeuvre
     * @param string  $manoeuvreDetail    Manoeuvre detail
     * @param string  $needNewStop        Need new stop
     * @param string  $newStopDetail      New stop detail
     * @param string  $hasNotFixedStop    Has not fixed stop
     * @param string  $notFixedStopDetail Not fixed stop detail
     * @param RefData $subsidised         Subsidised
     * @param string  $subsidyDetail      Subsidy detail
     *
     * @return bool
     */
    public function updateStops(
        $useAllStops,
        $hasManoeuvre,
        $manoeuvreDetail,
        $needNewStop,
        $newStopDetail,
        $hasNotFixedStop,
        $notFixedStopDetail,
        $subsidised,
        $subsidyDetail
    ) {
        $this->canEdit();

        $this->setUseAllStops($useAllStops);
        $this->setHasManoeuvre($hasManoeuvre);
        $this->setManoeuvreDetail($manoeuvreDetail);
        $this->setNeedNewStop($needNewStop);
        $this->setNewStopDetail($newStopDetail);
        $this->setHasNotFixedStop($hasNotFixedStop);
        $this->setNotFixedStopDetail($notFixedStopDetail);
        $this->setSubsidised($subsidised);
        $this->setSubsidyDetail($subsidyDetail);

        return true;
    }

    /**
     * Update quality schemes
     *
     * @param string $isQualityPartnership             Is quality partnership
     * @param string $qualityPartnershipDetails        Quality partnership details
     * @param string $qualityPartnershipFacilitiesUsed Quality partnership facilities used
     * @param string $isQualityContract                Is quality contract
     * @param string $qualityContractDetails           Quality contract details
     *
     * @return bool
     */
    public function updateQualitySchemes(
        $isQualityPartnership,
        $qualityPartnershipDetails,
        $qualityPartnershipFacilitiesUsed,
        $isQualityContract,
        $qualityContractDetails
    ) {
        $this->canEdit();

        $this->setIsQualityPartnership($isQualityPartnership);
        $this->setQualityPartnershipDetails($qualityPartnershipDetails);
        $this->setQualityPartnershipFacilitiesUsed($qualityPartnershipFacilitiesUsed);
        $this->setIsQualityContract($isQualityContract);
        $this->setQualityContractDetails($qualityContractDetails);

        return true;
    }

    /**
     * Update TA Authority
     *
     * @param string $stoppingArrangements Stopping arrangements
     *
     * @return bool
     */
    public function updateTaAuthority($stoppingArrangements)
    {
        $this->canEdit();

        $this->stoppingArrangements = $stoppingArrangements;

        return true;
    }

    /**
     * Update service details
     *
     * @param string                $serviceNo       Service no
     * @param string                $startPoint      Start point
     * @param string                $finishPoint     Finish point
     * @param string                $via             Via
     * @param string                $otherDetails    Other details
     * @param string                $receivedDate    Received date
     * @param string                $effectiveDate   Effective date
     * @param string                $endDate         End date
     * @param BusNoticePeriodEntity $busNoticePeriod Bus notice period
     *
     * @return bool
     */
    public function updateServiceDetails(
        $serviceNo,
        $startPoint,
        $finishPoint,
        $via,
        $otherDetails,
        $receivedDate,
        $effectiveDate,
        $endDate,
        $busNoticePeriod
    ) {
        $this->canEdit();

        $this->serviceNo = $serviceNo;
        $this->startPoint = $startPoint;
        $this->finishPoint = $finishPoint;
        $this->via = $via;
        $this->otherDetails = $otherDetails;
        $this->busNoticePeriod = $busNoticePeriod;

        $receivedDateTime = \DateTime::createFromFormat('Y-m-d', $receivedDate);
        $effectiveDateTime = \DateTime::createFromFormat('Y-m-d', $effectiveDate);
        $endDateTime = \DateTime::createFromFormat('Y-m-d', $endDate);

        if (!$receivedDateTime instanceof \DateTime) {
            $receivedDateTime = null;
        }

        if (!$effectiveDateTime instanceof \DateTime) {
            $effectiveDateTime = null;
        }

        if (!$endDateTime instanceof \DateTime) {
            $endDateTime = null;
        }

        $this->receivedDate = $receivedDateTime;
        $this->effectiveDate = $effectiveDateTime;
        $this->endDate = $endDateTime;

        $this->isShortNotice = 'N';

        if ($this->isShortNotice($effectiveDateTime, $receivedDateTime, $busNoticePeriod)) {
            $this->isShortNotice = 'Y';
        }

        return true;
    }

    /**
     * Update service register
     *
     * @param string $trcConditionChecked Trc condition checked
     * @param string $trcNotes            Trc notes
     * @param string $copiedToLaPte       Copied to la pte
     * @param string $laShortNote         La short note
     * @param string $opNotifiedLaPte     Op notified la pte
     * @param string $applicationSigned   Application signed
     * @param string $timetableAcceptable Timetable acceptable
     * @param string $mapSupplied         Map supplied
     * @param string $routeDescription    Route description
     *
     * @return BusReg
     */
    public function updateServiceRegister(
        $trcConditionChecked,
        $trcNotes,
        $copiedToLaPte,
        $laShortNote,
        $opNotifiedLaPte,
        $applicationSigned,
        $timetableAcceptable = null,
        $mapSupplied = null,
        $routeDescription = null
    ) {
        if (!$this->isLatestVariation()) {
            throw new ForbiddenException('No permission to edit this record');
        }

        $this->trcConditionChecked = $trcConditionChecked;
        $this->trcNotes = $trcNotes;
        $this->copiedToLaPte = $copiedToLaPte;
        $this->laShortNote = $laShortNote;
        $this->opNotifiedLaPte = $opNotifiedLaPte;
        $this->applicationSigned = $applicationSigned;

        if ($timetableAcceptable !== null) {
            $this->timetableAcceptable = $timetableAcceptable;
        }

        if ($mapSupplied !== null) {
            $this->mapSupplied = $mapSupplied;
        }

        if ($routeDescription !== null) {
            $this->routeDescription = $routeDescription;
        }

        return $this;
    }

    /**
     * Is short notice
     *
     * @param \DateTime             $effectiveDate Effective date
     * @param \DateTime             $receivedDate  Received date
     * @param BusNoticePeriodEntity $busRules      Bus rules
     *
     * @return bool|null
     */
    private function isShortNotice($effectiveDate, $receivedDate, BusNoticePeriodEntity $busRules)
    {
        if (!($effectiveDate instanceof \DateTime) || !($receivedDate instanceof \DateTime)) {
            return false;
        }

        $standardPeriod = $busRules->getStandardPeriod();

        if ($standardPeriod > 0) {
            $receivedDate = clone $receivedDate;
            $interval = new \DateInterval('P' . $standardPeriod . 'D');

            if ($receivedDate->add($interval)->setTime(0, 0) >= $effectiveDate->setTime(0, 0)) {
                return true;
            }
        }

        $cancellationPeriod = $busRules->getCancellationPeriod();
        $variationNo = $this->getVariationNo();

        if ($cancellationPeriod > 0 && $variationNo > 0) {
            $parent = $this->getParent();

            if (!$parent || empty($parent->getEffectiveDate())) {
                //if we don't have a parent record, the result is undefined.
                return null;
            }

            $lastDateTime
                = ($parent->getEffectiveDate() instanceof \DateTime)
                    ? clone $parent->getEffectiveDate() : new \DateTime($parent->getEffectiveDate());

            $interval = new \DateInterval('P' . $cancellationPeriod . 'D');

            if ($lastDateTime->add($interval)->setTime(0, 0) >= $effectiveDate->setTime(0, 0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Populates the short notice field
     *
     * @return void
     */
    public function populateShortNotice()
    {
        $this->isShortNotice = 'N';

        $effectiveDate = $this->processDate($this->effectiveDate);
        $receivedDate = $this->processDate($this->receivedDate);

        if ($this->isShortNotice($effectiveDate, $receivedDate, $this->busNoticePeriod)) {
            $this->isShortNotice = 'Y';
        }
    }

    /**
     * Process date
     *
     * @param string $date     Date
     * @param string $format   Format
     * @param bool   $zeroTime Zero time
     *
     * @return \DateTime|null
     */
    public function processDate($date, $format = 'Y-m-d', $zeroTime = true)
    {
        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!$dateTime instanceof \DateTime) {
            return null;
        }

        if ($zeroTime) {
            $dateTime->setTime(0, 0, 0);
        }

        return $dateTime;
    }

    /**
     * Returns whether the record is short notice refused
     *
     * @return bool
     */
    public function isShortNoticeRefused()
    {
        return ($this->shortNoticeRefused === 'Y' ? true : false);
    }

    /**
     * Get decision
     *
     * @return array|null
     */
    public function getDecision()
    {
        $decisionTaken = true;
        $reason = null;

        switch ($this->status->getId()) {
            case self::STATUS_REFUSED:
                $reason = ($this->isShortNoticeRefused()) ? $this->reasonSnRefused : $this->reasonRefused;
                break;
            case self::STATUS_CANCELLED:
            case self::STATUS_ADMIN:
                $reason = $this->reasonCancelled;
                break;
            case self::STATUS_WITHDRAWN:
                if ($this->withdrawnReason !== null) {
                    $reason = $this->withdrawnReason->getDescription();
                }
                break;
            default:
                $decisionTaken = false;
                break;
        }

        return ($decisionTaken) ? [
            'decision' => $this->status->getDescription(),
            'reason' => $reason,
        ] : null;
    }

    /**
     * Returns whether a bus reg may be granted
     *
     * @param FeeEntity $fee Fee
     *
     * @return bool
     */
    public function isGrantable(FeeEntity $fee = null)
    {
        if (false === $this->isGrantableBasedOnRequiredFields()) {
            // bus reg without all required fields which makes it non-grantable
            return false;
        }

        if (false === $this->isGrantableBasedOnShortNotice()) {
            // bus reg without short notice details or with one which makes it non-grantable
            return false;
        }

        if ((false === $this->isGrantableBasedOnFee($fee))) {
            // bus reg with a fee which makes it non-grantable
            return false;
        }

        return true;
    }

    /**
     * Returns whether a bus reg has all required fields which makes it grantable
     *
     * @return bool
     */
    private function isGrantableBasedOnRequiredFields()
    {
        // mandatory fields which needs to be marked as Yes
        $yesFields = [
            'timetableAcceptable',
            'mapSupplied',
            'trcConditionChecked',
            'copiedToLaPte',
            'applicationSigned'
        ];

        if ($this->isScottishRules()) {
            // for Scottish short notice registrations opNotifiedLaPte is required
            $yesFields[] = 'opNotifiedLaPte';
        }

        foreach ($yesFields as $field) {
            if (empty($this->$field) || $this->$field !== 'Y') {
                return false;
            }
        }

        // mandatory fields which can't be empty
        $nonEmptyFields = [
            'effectiveDate',
            'receivedDate',
            'serviceNo',
            'startPoint',
            'finishPoint',
        ];

        foreach ($nonEmptyFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // mandatory collections which can't be empty
        $nonEmptyCollections = [
            'busServiceTypes',
            'trafficAreas',
            'localAuthoritys',
        ];

        foreach ($nonEmptyCollections as $field) {
            if (($this->$field === null) || $this->$field->isEmpty()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether a bus reg has short notice details which makes it grantable
     *
     * @return bool
     */
    private function isGrantableBasedOnShortNotice()
    {
        if ($this->isShortNotice !== 'Y') {
            // not a short notice one
            return true;
        }

        if (empty($this->shortNotice)) {
            // no short notice details makes it non-grantable
            return false;
        }

        if ($this->laShortNote !== 'Y') {
            return false;
        }

        return $this->shortNotice->hasGrantableDetails();
    }

    /**
     * Returns whether a bus reg has a fee which makes it grantable
     *
     * @param FeeEntity $fee Fee
     *
     * @return bool
     */
    private function isGrantableBasedOnFee(FeeEntity $fee = null)
    {
        if (empty($fee)) {
            // no fee makes it grantable
            return true;
        }

        if ($fee->getFeeStatus()->getId() === FeeEntity::STATUS_PAID) {
            // the fee is paid
            return true;
        }

        return false;
    }

    /**
     * Update status
     *
     * @param RefData $status Status
     *
     * @return BusReg
     */
    private function updateStatus(RefData $status)
    {
        $this->setRevertStatus($this->getStatus());
        $this->setStatus($status);
        $this->setStatusChangeDate(new \DateTime());

        return $this;
    }

    /**
     * Resets status
     *
     * @return BusReg
     */
    public function resetStatus()
    {
        $this->canMakeDecision();

        $this->updateStatus($this->getRevertStatus());

        return $this;
    }

    /**
     * Admin cancel
     *
     * @param RefData $status Status
     * @param string  $reason Reason
     *
     * @throws BadRequestException
     * @return BusReg
     */
    public function cancelByAdmin(RefData $status, $reason)
    {
        $this->canMakeDecision();

        if ($status->getId() !== self::STATUS_ADMIN) {
            throw new BadRequestException('Please provide a valid status');
        }

        $this->updateStatus($status);
        $this->setReasonCancelled($reason);

        return $this;
    }

    /**
     * Withdraw
     *
     * @param RefData $status Status
     * @param RefData $reason Reason
     *
     * @throws BadRequestException
     * @return BusReg
     */
    public function withdraw(RefData $status, RefData $reason)
    {
        $this->canMakeDecision();

        if ($status->getId() !== self::STATUS_WITHDRAWN) {
            throw new BadRequestException('Please provide a valid status');
        }

        $this->updateStatus($status);
        $this->setWithdrawnReason($reason);

        return $this;
    }

    /**
     * Refuse
     *
     * @param RefData $status Status
     * @param string  $reason Reason
     *
     * @throws BadRequestException
     * @return BusReg
     */
    public function refuse(RefData $status, $reason)
    {
        $this->canMakeDecision();

        if ($status->getId() !== self::STATUS_REFUSED) {
            throw new BadRequestException('Please provide a valid status');
        }

        $this->updateStatus($status);
        $this->setReasonRefused($reason);

        return $this;
    }

    /**
     * Refuse by Short Notice
     *
     * @param string $reason Reason
     *
     * @return BusReg
     */
    public function refuseByShortNotice($reason)
    {
        $this->canMakeDecision();

        $this->setShortNoticeRefused('Y');
        $this->setReasonSnRefused($reason);
        $this->setEffectiveDate($this->calculateNoticeDate());

        // reset the short notice record
        $this->setIsShortNotice('N');

        if ($this->getShortNotice() !== null) {
            $this->getShortNotice()->reset();
        }

        return $this;
    }

    /**
     * Calculates the short notice date
     *
     * @return null|string
     */
    private function calculateNoticeDate()
    {
        $receivedDateTime = $this->processDate($this->receivedDate);

        if (!($receivedDateTime instanceof \DateTime)) {
            return null;
        }

        if ($this->busNoticePeriod === null) {
            return null;
        }

        if (($this->busNoticePeriod->getCancellationPeriod() > 0) && ($this->variationNo > 0)) {
            if ($this->parent === null) {
                // if we don't have a parent record, the result is undefined.
                return null;
            }

            $lastDateTime = $this->processDate($this->parent->getEffectiveDate());
            $interval = new \DateInterval('P' . $this->busNoticePeriod->getCancellationPeriod() . 'D');

            return $lastDateTime->add($interval);
        }

        if ($this->busNoticePeriod->getStandardPeriod() > 0) {
            $interval = new \DateInterval('P' . $this->busNoticePeriod->getStandardPeriod() . 'D');

            return $receivedDateTime->add($interval);
        }

        return $this->effectiveDate;
    }

    /**
     * Grant
     *
     * @param RefData $status           Status
     * @param array   $variationReasons Variation reasons
     *
     * @throws BadRequestException
     * @return BusReg
     */
    public function grant(RefData $status, $variationReasons = null)
    {
        $this->canMakeDecision();

        if ($this->isGrantable() !== true) {
            throw new BadRequestException('The Bus Reg is not grantable');
        }

        if ($status->getId() !== $this->getStatusForGrant()) {
            throw new BadRequestException('The Bus Reg is not grantable');
        }

        if ($this->status->getId() === self::STATUS_VAR) {
            $this->setVariationReasons($variationReasons);
        }

        $this->updateStatus($status);

        return $this;
    }

    /**
     * Get status for grant action
     *
     * @return string
     */
    public function getStatusForGrant()
    {
        return (!empty(self::$grantStatusMap[$this->status->getId()]))
            ? self::$grantStatusMap[$this->status->getId()] : null;
    }

    /**
     * Get context value
     *
     * @return string
     */
    public function getContextValue()
    {
        return $this->getLicence()->getLicNo();
    }

    /**
     * Add other service number
     *
     * @param string $serviceNo Service No
     *
     * @return void
     */
    public function addOtherServiceNumber($serviceNo)
    {
        $otherServiceEntity = new BusRegOtherService($this, $serviceNo);
        $this->otherServices->add($otherServiceEntity);
    }

    /**
     * Gets the publication section for a grant/cancellation email
     *
     * @return int
     * @throws RuntimeException
     */
    public function getPublicationSectionForGrantEmail()
    {
        $currentStatus = $this->status->getId();
        $allowableCurrentStatus = [self::STATUS_REGISTERED, self::STATUS_CANCELLED];

        if (!in_array($currentStatus, $allowableCurrentStatus)) {
            throw new RuntimeException('valid statuses for grant email are registered and cancelled');
        }

        $revertStatus = $this->revertStatus->getId();
        $shortNotice = $this->isShortNotice;

        switch ($revertStatus) {
            case self::STATUS_NEW:
            case self::STATUS_VAR:
                return $this->getPublicationSectionForGrantEmailRegistered($revertStatus, $shortNotice);
            case self::STATUS_CANCEL:
                return $this->getPublicationSectionForGrantEmailCancelled($shortNotice);
        }

        throw new RuntimeException('valid revert statuses for grant email are new, variation or cancellation');
    }

    /**
     * Get publication section for grant email registered
     *
     * @param string $revertStatus Revert status
     * @param string $shortNotice  Short notice
     *
     * @return int
     * @throws RuntimeException
     */
    private function getPublicationSectionForGrantEmailRegistered($revertStatus, $shortNotice)
    {
        if (!$this->isRegistered()) {
            throw new RuntimeException('status mismatch generating registered grant email');
        }

        if ($revertStatus === self::STATUS_NEW) {
            return ($shortNotice == 'Y' ?
                PublicationSectionEntity::BUS_NEW_SHORT_SECTION :
                PublicationSectionEntity::BUS_NEW_SECTION
            );
        } else {
            return ($shortNotice == 'Y' ?
                PublicationSectionEntity::BUS_VAR_SHORT_SECTION :
                PublicationSectionEntity::BUS_VAR_SECTION
            );
        }
    }

    /**
     * Get publication section for grant email cancelled
     *
     * @param string $shortNotice Short notice
     *
     * @return int
     * @throws RuntimeException
     */
    private function getPublicationSectionForGrantEmailCancelled($shortNotice)
    {
        if ($this->status->getId() !== self::STATUS_CANCELLED) {
            throw new RuntimeException('status mismatch generating cancellation grant email');
        }

        return ($shortNotice == 'Y' ?
            PublicationSectionEntity::BUS_CANCEL_SHORT_SECTION :
            PublicationSectionEntity::BUS_CANCEL_SECTION
        );
    }

    /**
     * Gets a string of publications affected by a grant action, called by the EBSR emails,
     * only relevant for certain emails, so often will return empty string
     *
     * @param PublicationSectionEntity $pubSection Pub section
     *
     * @return string
     */
    public function getPublicationLinksForGrantEmail(PublicationSectionEntity $pubSection)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where($expr->eq('publicationSection', $pubSection));

        $matchingLinks = $this->publicationLinks->matching($criteria);
        $matchingPublications = [];

        /** @var PublicationLinkEntity $link */
        foreach ($matchingLinks as $link) {
            $matchedPublication = $link->getPublication();

            //only include new publications, and ignore duplicates by having pubNo as the array key
            if ($matchedPublication->isNew()) {
                $pubNo = $matchedPublication->getPublicationNo();
                $pubTa = $matchedPublication->getTrafficArea()->getName();
                $matchingPublications[$pubNo] = $pubNo . ' ' . $pubTa;
            }
        }

        return implode(', ', $matchingPublications);
    }

    /**
     * Return a list of service numbers formatted with pattern: serviceNo(serviceNo2,serviceNo3,serviceNo4)
     *
     * @return string
     */
    public function getFormattedServiceNumbers()
    {
        $additional = [];

        /** @var BusRegOtherServiceEntity $otherService */
        foreach ($this->otherServices as $otherService) {
            $additional[] = $otherService->getServiceNo();
        }

        if (!empty($additional)) {
            return $this->serviceNo . '(' . implode(',', $additional) . ')';
        }

        return $this->serviceNo;
    }

    /**
     * Get organisations this entity is linked to
     *
     * @return \Dvsa\Olcs\Api\Entity\Organisation\Organisation|null
     */
    public function getRelatedOrganisation()
    {
        if ($this->getLicence()) {
            return $this->getLicence()->getRelatedOrganisation();
        }

        return null;
    }

    /**
     * Flag for raising fees. Based on the current status.
     *
     * @note Be careful where in the code this is called. The current status determines whether a fee is generated so
     * ensure it is called at the appropriate point in the code. ie. with the new status being applied.
     *
     * @return bool
     */
    public function isChargeableStatus()
    {
        $busStatus = $this->getStatus()->getId();

        if ($busStatus === self::STATUS_NEW || $busStatus === self::STATUS_VAR) {
            return true;
        }

        return false;
    }
}
