<?php

namespace Dvsa\Olcs\Api\Entity\Bus;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as BusShortNoticeEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService as BusRegOtherServiceEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;

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
class BusReg extends AbstractBusReg implements ContextProviderInterface
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
     * @param LicenceEntity $licence
     * @param RefData $status
     * @param RefData $revertStatus
     * @param RefData $subsidised
     * @param BusNoticePeriodEntity $busNoticePeriod
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

    public function canCreateVariation()
    {
        if ($this->status->getId() === self::STATUS_REGISTERED) {
            return true;
        }

        return false;
    }

    /**
     * @param RefData $status
     * @param RefData $revertStatus
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
            $newOtherService->setBusReg($busReg);

            $otherServices->add($newOtherService);
        }
        $busReg->setOtherServices($otherServices);

        return $busReg;
    }

    /**
     * Populate properties from data
     *
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
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
     * @param $useAllStops
     * @param $hasManoeuvre
     * @param $manoeuvreDetail
     * @param $needNewStop
     * @param $newStopDetail
     * @param $hasNotFixedStop
     * @param $notFixedStopDetail
     * @param $subsidised
     * @param $subsidyDetail
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
     * @param $isQualityPartnership
     * @param $qualityPartnershipDetails
     * @param $qualityPartnershipFacilitiesUsed
     * @param $isQualityContract
     * @param $qualityContractDetails
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
     * @param $stoppingArrangements
     * @return bool
     */
    public function updateTaAuthority($stoppingArrangements)
    {
        $this->canEdit();

        $this->stoppingArrangements = $stoppingArrangements;

        return true;
    }

    public function updateServiceDetails(
        $serviceNo,
        $startPoint,
        $finishPoint,
        $via,
        $otherDetails,
        $receivedDate,
        $effectiveDate,
        $endDate,
        $busNoticePeriod,
        $busRules
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

        if ($this->isShortNotice($effectiveDateTime, $receivedDateTime, $busRules)) {
            $this->isShortNotice = 'Y';
        }

        return true;
    }

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
     * @param \DateTime $effectiveDate
     * @param \DateTime $receivedDate
     * @param BusNoticePeriodEntity $busRules
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

            if (!$parent) {
                //if we don't have a parent record, the result is undefined.
                return null;
            }

            $lastDateTime = clone $parent->getEffectiveDate();
            $interval = new \DateInterval('P' . $cancellationPeriod . 'D');

            if ($lastDateTime->add($interval)->setTime(0, 0) >= $effectiveDate->setTime(0, 0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Populates the short notice field
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
     * @param string $date
     * @param string $format
     * @param bool $zeroTime
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
     * @return array|null
     */
    public function getDecision()
    {
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
                $reason = $this->withdrawnReason->getDescription();
                break;
        }

        return ($reason !== null) ? [
            'decision' => $this->status->getDescription(),
            'reason' => $reason,
        ] : null;
    }

    /**
     * Returns whether a bus reg may be granted
     *
     * @param FeeEntity $fee
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
            'laShortNote',
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

        return $this->shortNotice->hasGrantableDetails();
    }

    /**
     * Returns whether a bus reg has a fee which makes it grantable
     *
     * @param FeeEntity $fee
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
     * @param RefData $status
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
     * @param RefData $status
     * @param string $reason
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
     * @param RefData $status
     * @param RefData $reason
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
     * @param RefData $status
     * @param string $reason
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
     * @param string $reason
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
     * @param RefData $status
     * @param array $variationReasons
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

    public function getContextValue()
    {
        return $this->getLicence()->getLicNo();
    }

    /**
     * @param string $serviceNo
     */
    public function addOtherServiceNumber($serviceNo)
    {
        $otherServiceEntity = new BusRegOtherService($this, $serviceNo);
        $this->otherServices->add($otherServiceEntity);
    }

    /**
     * Returns a formatted string of service numbers
     *
     * @return string
     */
    public function getFormattedServiceNumbers()
    {
        $serviceNumbers = $this->serviceNo;
        $otherServiceNumbers = $this->getOtherServices();
        $extractedNumbers = [];

        /** @var BusRegOtherServiceEntity $number */
        foreach ($otherServiceNumbers as $number) {
            $extractedNumbers[] = $number->getServiceNo();
        }

        if (!empty($extractedNumbers)) {
            $serviceNumbers .= ' (' . implode(', ', $extractedNumbers) . ')';
        }

        return $serviceNumbers;
    }
}
