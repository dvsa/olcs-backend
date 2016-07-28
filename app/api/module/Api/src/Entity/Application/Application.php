<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceNoGen;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;
use Zend\Filter\Word\CamelCaseToUnderscore;

/**
 * Application Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application",
 *    indexes={
 *        @ORM\Index(name="ix_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_application_interim_status", columns={"interim_status"}),
 *        @ORM\Index(name="ix_application_withdrawn_reason", columns={"withdrawn_reason"}),
 *        @ORM\Index(name="ix_application_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_application_applied_via", columns={"applied_via"}),
 *        @ORM\Index(name="ix_application_psv_which_vehicle_sizes", columns={"psv_which_vehicle_sizes"})
 *    }
 * )
 */
class Application extends AbstractApplication implements ContextProviderInterface, OrganisationProviderInterface
{
    const ERROR_NI_NON_GOODS = 'AP-TOL-1';
    const ERROR_GV_NON_SR = 'AP-TOL-2';
    const ERROR_VAR_UNCHANGE_NI = 'AP-TOL-3';
    const ERROR_VAR_UNCHANGE_OT = 'AP-TOL-4';
    const ERROR_REQUIRES_CONFIRMATION = 'AP-TOL-5';
    const ERROR_OT_REQUIRED = 'AP-TOL-6';
    const ERROR_FINANCIAL_HISTORY_DETAILS_REQUIRED = 'AP-FH-1';
    const ERROR_SAFE_REQUIRE_CONFIRMATION = 'AP-SAFE-1';
    const ERROR_NO_VEH_ENTERED = 'AP-VEH-1';

    const APPLICATION_STATUS_NOT_SUBMITTED = 'apsts_not_submitted';
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';
    const APPLICATION_STATUS_VALID = 'apsts_valid';
    const APPLICATION_STATUS_WITHDRAWN = 'apsts_withdrawn';
    const APPLICATION_STATUS_REFUSED = 'apsts_refused';
    const APPLICATION_STATUS_NOT_TAKEN_UP = 'apsts_ntu';
    const APPLICATION_STATUS_CURTAILED = 'apsts_curtailed';
    const APPLICATION_STATUS_CANCELLED = 'apsts_cancelled';

    const INTERIM_STATUS_REQUESTED = 'int_sts_requested';
    const INTERIM_STATUS_INFORCE = 'int_sts_in_force';
    const INTERIM_STATUS_REFUSED = 'int_sts_refused';
    const INTERIM_STATUS_REVOKED = 'int_sts_revoked';
    const INTERIM_STATUS_GRANTED = 'int_sts_granted';
    const INTERIM_STATUS_ENDED = 'int_sts_ended';

    const VARIATION_STATUS_UNCHANGED = 0;
    const VARIATION_STATUS_REQUIRES_ATTENTION = 1;
    const VARIATION_STATUS_UPDATED = 2;

    const APPLICATION_TYPE_NEW = 0;
    const APPLICATION_TYPE_VARIATION = 1;

    // @to-do this needs moving to ref data
    const APPLICATION_TYPE_NEW_DESCRIPTION = 'New';
    const APPLICATION_TYPE_VARIATION_DESCRIPTION = 'Variation';

    const CODE_GV_APP = 'GV79';
    const CODE_GV_VAR_UPGRADE = 'GV80A';
    const CODE_GV_VAR_NO_UPGRADE = 'GV81';
    const CODE_PSV_APP = 'PSV421';
    const CODE_PSV_APP_SR = 'PSV356';
    const CODE_PSV_VAR_UPGRADE    = 'PSV431A';
    const CODE_PSV_VAR_NO_UPGRADE = 'PSV431';

    const NOT_APPLICABLE = 'Not applicable';
    const UNKNOWN = 'Unknown';

    const TARGET_COMPLETION_TIME = '+9 week';

    const APPLIED_VIA_SELFSERVE = 'applied_via_selfserve';
    const APPLIED_VIA_POST = 'applied_via_post';
    const APPLIED_VIA_PHONE = 'applied_via_phone';

    const PSV_VEHICLE_SIZE_SMALL = 'psvvs_small';
    const PSV_VEHICLE_SIZE_MEDIUM_LARGE = 'psvvs_medium_large';
    const PSV_VEHICLE_SIZE_BOTH = 'psvvs_both';

    const DATE_FORMAT = 'd M Y';

    /**
     * Publication No
     *
     * @var integer
     */
    protected $publicationNo;

    /**
     * Out of objection date
     * @var string
     */
    protected $oooDate;

    /**
     * Out of representation date
     * @var string
     */
    protected $oorDate;

    /**
     * isOpposed
     * @var bool
     */
    protected $isOpposed;

    /**
     * publishedDate
     * @var string
     */
    protected $publishedDate;

    public function __construct(Licence $licence, RefData $status, $isVariation)
    {
        parent::__construct();

        $this->setLicence($licence);
        $this->setStatus($status);
        $this->setIsVariation($isVariation);
    }

    public function updateTypeOfLicence($niFlag, $goodsOrPsv, $licenceType)
    {
        if ($this->validateTol($niFlag, $goodsOrPsv, $licenceType)) {
            $this->setNiFlag($niFlag);
            $this->setGoodsOrPsv($goodsOrPsv);
            $this->setLicenceType($licenceType);
            return true;
        }
    }

    public function isValidTol($niFlag, $goodsOrPsv, $licenceType)
    {
        try {
            return $this->validateTol($niFlag, $goodsOrPsv, $licenceType);
        } catch (ValidationException $ex) {
            return false;
        }
    }

    public function validateTol($niFlag, $goodsOrPsv, $licenceType)
    {
        $errors = [];

        if (!$goodsOrPsv) {
            $errors['goodsOrPsv'][] =[
                self::ERROR_OT_REQUIRED => 'Operator type is required'
            ];
            // need to throw exception ealier if operator type is empty
            throw new ValidationException($errors);
        }
        if ($niFlag === 'Y' && $goodsOrPsv->getId() === Licence::LICENCE_CATEGORY_PSV) {
            $errors['goodsOrPsv'][] =[
                self::ERROR_NI_NON_GOODS => 'NI can only apply for goods licences'
            ];
        }

        if ($goodsOrPsv->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            && $licenceType->getId() === Licence::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            $errors['licenceType'][] =[
                self::ERROR_GV_NON_SR => 'GV operators cannot apply for special restricted licences'
            ];
        }

        if ($this->getIsVariation()) {
            if ($this->getGoodsOrPsv() != $goodsOrPsv) {
                $errors['goodsOrPsv'][] =[
                    self::ERROR_GV_NON_SR => 'GV operators cannot apply for special restricted licences'
                ];
            }

            if ($this->getNiFlag() != $niFlag) {
                $errors['niFlag'][] =[
                    self::ERROR_GV_NON_SR => 'GV operators cannot apply for special restricted licences'
                ];
            }
        }

        if (empty($errors)) {
            return true;
        }

        throw new ValidationException($errors);
    }

    /**
     * @param \Dvsa\Olcs\Api\Entity\System\Category $category
     * @param \Dvsa\Olcs\Api\Entity\System\SubCategory $subCategory
     * 
     * @return Collection
     */
    public function getApplicationDocuments($category, $subCategory)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where($expr->eq('category', $category));
        $criteria->andWhere(
            $expr->eq('subCategory', $subCategory)
        );

        return $this->documents->matching($criteria);
    }

    public function updateFinancialHistory(
        $bankrupt,
        $liquidation,
        $receivership,
        $administration,
        $disqualified,
        $insolvencyDetails,
        $insolvencyConfirmation
    ) {
        $flags = compact('bankrupt', 'liquidation', 'receivership', 'administration', 'disqualified');
        if ($this->validateFinancialHistory($flags, $insolvencyDetails)) {
            foreach ($flags as $key => $flag) {
                $this->{$key} = $flag;
            }
            $this->setInsolvencyDetails($insolvencyDetails);
            if ($insolvencyConfirmation) {
                $this->setInsolvencyConfirmation('Y');
            }
            return true;
        }
    }

    protected function validateFinancialHistory($flags, $insolvencyDetails)
    {
        $foundYes = false;
        foreach ($flags as $element) {
            if ($element == 'Y') {
                $foundYes = true;
                break;
            }
        }
        if (!$foundYes) {
            return true;
        }
        if (strlen($insolvencyDetails) >= 200) {
            return true;
        }
        $errors = [
            'insolvencyDetails' => [
                self::ERROR_FINANCIAL_HISTORY_DETAILS_REQUIRED =>
                    'You selected \'yes\' in one of the provided questions, so the input has to be at least 200
                characters long'
            ]
        ];
        throw new ValidationException($errors);
    }

    public function getOtherLicencesByType($type)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expr->eq('previousLicenceType', $type));

        return $this->otherLicences->matching($criteria);
    }

    /**
     * Determine whether the licence has changed within set parameters that would
     * qualify this variation to be an interim.
     *
     * @return bool
     */
    protected function hasUpgrade()
    {
        $applicationLicenceTypeId = null;
        if ($this->getLicenceType()) {
            $applicationLicenceTypeId = $this->getLicenceType()->getId();
        }

        $licenceTypeId = null;
        if ($this->getLicence()->getLicenceType()) {
            $licenceTypeId = $this->getLicence()->getLicenceType()->getId();
        }

        // If licence type has been changed from restricted to national or international.
        if (
            $licenceTypeId === Licence::LICENCE_TYPE_RESTRICTED &&
            (
                $applicationLicenceTypeId === Licence::LICENCE_TYPE_STANDARD_NATIONAL ||
                $applicationLicenceTypeId === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ) {
            return true;
        }

        // If licence is is updated from a standard national to an international.
        if (
            $licenceTypeId === Licence::LICENCE_TYPE_STANDARD_NATIONAL &&
            (
                $applicationLicenceTypeId === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Has the overall number of vehicles authority increased.
     *
     * @return bool
     */
    protected function hasAuthVehiclesIncrease()
    {
        return ((int) $this->getTotAuthVehicles() > (int) $this->getLicence()->getTotAuthVehicles());
    }

    /**
     * Has the overall number of trailers authority increased.
     *
     * @return bool
     */
    protected function hasAuthTrailersIncrease()
    {
        return ($this->getTotAuthTrailers() > $this->getLicence()->getTotAuthTrailers());
    }

    /**
     * Does this variation specify an additional operating centre.
     *
     * @return bool
     */
    protected function hasNewOperatingCentre()
    {
        $operatingCentres = $this->getOperatingCentres();
        /* @var $operatingCentre ApplicationOperatingCentre */
        foreach ($operatingCentres as $operatingCentre) {
            if ($operatingCentre->getAction() === ApplicationOperatingCentre::ACTION_ADD) {
                return true;
            }
        }

        return false;
    }

    /**
     * Does this variation increment an operating centres vehicles or trailers.
     *
     * @return bool
     */
    protected function hasIncreaseInOperatingCentre()
    {
        $licence = array();
        $variation = array();

        // Makes dealing with the records easier.
        /* @var $lOperatingCentre \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre */
        foreach ($this->getLicence()->getOperatingCentres() as $lOperatingCentre) {
            $licence[$lOperatingCentre->getOperatingCentre()->getId()] = $lOperatingCentre;
        }

        /* @var $aOperatingCentre ApplicationOperatingCentre */
        foreach ($this->getOperatingCentres() as $aOperatingCentre) {
            $variation[$aOperatingCentre->getOperatingCentre()->getId()] = $aOperatingCentre;
        }

        // foreach of the licence op centres.
        foreach (array_keys($licence) as $operatingCenterId) {
            // If a variation record doesnt exists or its a removal op centre.
            if (!isset($variation[$operatingCenterId]) ||
                $variation[$operatingCenterId]->getAction() ===  ApplicationOperatingCentre::ACTION_DELETE
            ) {
                continue;
            }

            if (
                ($variation[$operatingCenterId]->getNoOfVehiclesRequired() >
                    $licence[$operatingCenterId]->getNoOfVehiclesRequired()) ||
                ($variation[$operatingCenterId]->getNoOfTrailersRequired() >
                    $licence[$operatingCenterId]->getNoOfTrailersRequired())
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Can this variation create an interim licence application?
     *
     * @return boolean
     */
    public function canHaveInterimLicence()
    {
        // only goods can have an interim
        if ($this->getGoodsOrPsv()->getId() !== Licence::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return false;
        }

        // if its an application then it can have an interim
        if (!$this->getIsVariation()) {
            return true;
        }

        if ($this->hasAuthVehiclesIncrease() ||
            $this->hasAuthTrailersIncrease() ||
            $this->hasUpgrade() ||
            $this->hasNewOperatingCentre() ||
            $this->hasIncreaseInOperatingCentre()
        ) {
            return true;
        }

        return false;
    }

    /**
     * If the application involves a licence upgrade
     *
     * @return boolean
     */
    public function isLicenceUpgrade()
    {
        // only a variation can be an upgrade
        if (!$this->isVariation()) {
            return false;
        }

        $restrictedUpgrades = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        return (
            $this->getLicence()->getLicenceType()->getId() === Licence::LICENCE_TYPE_RESTRICTED
            && in_array($this->getLicenceType()->getId(), $restrictedUpgrades)
        );
    }

    public function isPsvDowngrade()
    {
        if ($this->isGoods() || $this->isRestricted() === false) {
            return false;
        }

        return $this->getLicence()->isRestricted() === false;
    }

    public function updateLicenceHistory(
        $prevHasLicence,
        $prevHadLicence,
        $prevBeenRefused,
        $prevBeenRevoked,
        $prevBeenAtPi,
        $prevBeenDisqualifiedTc,
        $prevPurchasedAssets
    ) {
        $this->prevHasLicence = $prevHasLicence;
        $this->prevHadLicence = $prevHadLicence;
        $this->prevBeenRefused = $prevBeenRefused;
        $this->prevBeenRevoked = $prevBeenRevoked;
        $this->prevBeenAtPi = $prevBeenAtPi;
        $this->prevBeenDisqualifiedTc = $prevBeenDisqualifiedTc;
        $this->prevPurchasedAssets = $prevPurchasedAssets;
    }

    public function getApplicationType()
    {
        if ($this->isVariation()) {
            return self::APPLICATION_TYPE_VARIATION;
        }

        return self::APPLICATION_TYPE_NEW;
    }

    /**
     * Until the application type is moved to ref data, this method aids the return of a sensible description for
     * display. Eventually there will be lots more logic around this, hence it lives here within the entity itself
     * and not tucked inside a table formatter.
     *
     * @to-do Replaced constants with ref data
     *
     * @return string
     */
    public function getApplicationTypeDescription()
    {
        if ($this->getApplicationType() === self::APPLICATION_TYPE_VARIATION) {
            return self::APPLICATION_TYPE_VARIATION_DESCRIPTION;
        }

        return self::APPLICATION_TYPE_NEW_DESCRIPTION;
    }

    public function getApplicationDate()
    {
        if ($this->getReceivedDate() === null) {
            return $this->getCreatedOn();
        }

        return $this->getReceivedDate();
    }

    public function canSubmit()
    {
        return $this->getStatus()->getId() === self::APPLICATION_STATUS_NOT_SUBMITTED;
    }

    /**
     * Returns true/false depending on whether a case can be created for the application
     *
     * @return bool
     */
    public function canCreateCase()
    {
        if ($this->getStatus()->getId() === self::APPLICATION_STATUS_NOT_SUBMITTED
            || $this->getLicence()->getLicNo() === null) {
            return false;
        }

        return true;
    }

    /**
     * Essentially an alias of getIsVariation()
     *
     * @return @boolean
     */
    public function isVariation()
    {
        return (boolean) $this->getIsVariation();
    }

    /**
     * Check if the application is for a new licence
     *
     * @return bool
     */
    public function isNew()
    {
        return !$this->isVariation();
    }

    /**
     * @return boolean
     */
    public function isGoods()
    {
        if ($this->getGoodsOrPsv()) {
            return $this->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE;
        }
    }

    /**
     * @return boolean
     */
    public function isPsv()
    {
        if ($this->getGoodsOrPsv()) {
            return $this->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_PSV;
        }
    }

    /**
     * @return boolean
     */
    public function isSpecialRestricted()
    {
        if ($this->getLicenceType()) {
            return $this->getLicenceType()->getId() === Licence::LICENCE_TYPE_SPECIAL_RESTRICTED;
        }
    }

    /**
     * @return boolean
     */
    public function isRestricted()
    {
        if ($this->getLicenceType() !== null) {
            return $this->getLicenceType()->getId() === Licence::LICENCE_TYPE_RESTRICTED;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isStandardNational()
    {
        if ($this->getLicenceType() !== null) {
            return $this->getLicenceType()->getId() === Licence::LICENCE_TYPE_STANDARD_NATIONAL;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isStandardInternational()
    {
        if ($this->getLicenceType() !== null) {
            return $this->getLicenceType()->getId() === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if ($this->isVariation()) {
            $isUpgrade = $this->isLicenceUpgrade();
            if ($this->isGoods()) {
                $code = $isUpgrade
                    ? self::CODE_GV_VAR_UPGRADE
                    : self::CODE_GV_VAR_NO_UPGRADE;
            } else {
                $code = $isUpgrade
                    ? self::CODE_PSV_VAR_UPGRADE
                    : self::CODE_PSV_VAR_NO_UPGRADE;
            }
        } else {
            // new app.
            if ($this->isGoods()) {
                $code = self::CODE_GV_APP;
            } else {
                if ($this->isSpecialRestricted()) {
                    $code = self::CODE_PSV_APP_SR;
                } else {
                    $code = self::CODE_PSV_APP;
                }
            }
        }

        return $code;
    }

    public function getRemainingSpaces()
    {
        $vehicles = $this->getActiveLicenceVehicles();

        return $this->getTotAuthVehicles() - $vehicles->count();
    }

    public function getActiveLicenceVehicles()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->isNull('removalDate')
        );

        return $this->getLicence()->getLicenceVehicles()->matching($criteria);
    }

    /**
     * Get all vehicles count
     *
     * @return int
     */
    public function getAllVehiclesCount()
    {
        return $this->getLicence()->getLicenceVehicles()->count();
    }

    /**
     * Is real upgrade
     *
     * @return bool
     */
    public function isRealUpgrade()
    {
        if (!$this->isVariation()) {
            return false;
        }

        // If we have upgraded from restricted
        if ($this->isLicenceUpgrade()) {
            return true;
        }

        // If we have upgraded from stand nat, to stand inter
        if ($this->getLicence()->getLicenceType()->getId() === Licence::LICENCE_TYPE_STANDARD_NATIONAL
            && $this->getLicenceType()->getId() === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            return true;
        }

        return false;
    }

    public function getOcForInspectionRequest()
    {
        $list = [];
        $deleted = [];

        $applicationOperatingCentres = $this->getOperatingCentres();
        foreach ($applicationOperatingCentres as $applicationOperatingCentre) {
            $id = $applicationOperatingCentre->getOperatingCentre()->getId();
            if ($applicationOperatingCentre->getAction() !== 'D') {
                $list[$id] = $applicationOperatingCentre->getOperatingCentre();
            } else {
                $deleted[] = $id;
            }
        }

        $licenceOperatingCentres = $this->getLicence()->getOperatingCentres();
        foreach ($licenceOperatingCentres as $licenceOperatingCentre) {
            $id = $licenceOperatingCentre->getOperatingCentre()->getId();
            if (!in_array($id, $deleted)) {
                $list[$id] = $licenceOperatingCentre->getOperatingCentre();
            }
        }

        return array_values($list);
    }

    public function getVariationCompletion()
    {
        if (!$this->isVariation()) {
            return null;
        }

        $applicationCompletion = $this->getApplicationCompletion()->serialize();

        $completions = [];
        $converter = new CamelCaseToUnderscore();
        foreach ($applicationCompletion as $key => $value) {
            if (preg_match('/^([a-zA-Z]+)Status$/', $key, $matches)) {
                $section = strtolower($converter->filter($matches[1]));
                $completions[$section] = (int)$value;
            }
        }

        return $completions;
    }

    /**
     * Determine the traffic area used for fee lookup.
     */
    public function getFeeTrafficAreaId()
    {
        $trafficArea = $this->getLicence()->getTrafficArea();

        if (!is_null($trafficArea)) {
            return $trafficArea->getId();
        }

        if ($this->getNiFlag() === 'Y') {
            return TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE;
        }

        return null;
    }

    public function hasVariationChanges()
    {
        $completion = $this->getApplicationCompletion();

        $data = $completion->serialize([]);

        foreach ($data as $key => $value) {
            if (preg_match('/^([a-zA-Z]+)Status$/', $key) && (int)$value !== self::VARIATION_STATUS_UNCHANGED) {
                return true;
            }
        }

        return false;
    }

    public function getSectionsRequiringAttention()
    {
        $completion = $this->getApplicationCompletion();
        $data = $completion->serialize([]);
        $sections = [];

        $filter = new CamelCaseToUnderscore();

        foreach ($data as $key => $value) {
            if (preg_match('/^([a-zA-Z]+)Status$/', $key, $matches)
                && (int)$value === self::VARIATION_STATUS_REQUIRES_ATTENTION
            ) {
                $sections[] = strtolower($filter->filter($matches[1]));
            }
        }

        return $sections;
    }

    public function getActiveVehicles()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->isNull('removalDate')
        );

        return $this->getLicenceVehicles()->matching($criteria);
    }

    public function copyInformationFromLicence(Licence $licence)
    {
        $this->setLicenceType($licence->getLicenceType());
        $this->setGoodsOrPsv($licence->getGoodsOrPsv());
        $this->setTotAuthTrailers($licence->getTotAuthTrailers());
        $this->setTotAuthVehicles($licence->getTotAuthVehicles());
        $this->setNiFlag($licence->getNiFlag());
    }

    /**
     * Should Deltas be used in the people section
     *
     * @return boolean
     */
    public function useDeltasInPeopleSection()
    {
        // if application/variation organisation is sole trader or partnership
        if ($this->getLicence()->getOrganisation()->isSoleTrader() ||
            $this->getLicence()->getOrganisation()->isPartnership()
            ) {
            return false;
        }

        // if is an application AND no current ApplicationOrganisationUsers AND no inforce licences
        if (!$this->getIsVariation() &&
            $this->getApplicationOrganisationPersons()->count() === 0 &&
            !$this->getLicence()->getOrganisation()->hasInforceLicences()
            ) {
                return false;
        }

        return true;
    }

    public function getCurrentInterimStatus()
    {
        $currentStatus = $this->getInterimStatus();
        return $currentStatus !== null ? $currentStatus->getId() : null;
    }

    /**
     * Get the Out Of Representation Date
     *
     * @return DateTime|string DateTime if date can be calculated, otherwise a string const NOT_APPLCABLE or UNKNOWN
     */
    public function getOutOfRepresentationDate()
    {
        // If PSV application then
        if ($this->isPsv()) {
            return self::NOT_APPLICABLE;
        }

        $updatedAddedOperatingCentres = 0;
        foreach ($this->getOperatingCentres() as $aoc) {
            if ($aoc->getAction() === 'A' || $aoc->getAction() === 'U') {
                $updatedAddedOperatingCentres++;
            }
        }

        // If a new goods application and if 0 operating centres have been added/updated then
        if (!$this->isVariation() && $updatedAddedOperatingCentres === 0) {
            return self::UNKNOWN;
        }

        // if a goods variation and 0 operating centres have been added/updated then
        if ($this->isVariation() && $updatedAddedOperatingCentres === 0) {
            return self::NOT_APPLICABLE;
        }

        // If a goods new/variation application and operating centres have been added/udpated:
        /* @var $aoc \Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre */
        $maximumDate = null;
        foreach ($this->getOperatingCentres() as $aoc) {
            $operatingCentreOorDate = $this->calcOperatingCentreOutOfRepresentationDate($aoc);

            // If 1 or more of the operating centres are 'Unknown' then the overall OOR date = 'Unknown'
            if ($operatingCentreOorDate === self::UNKNOWN) {
                return self::UNKNOWN;
            }

            if ($operatingCentreOorDate === self::NOT_APPLICABLE) {
                continue;
            }

            // store the maximum (newest) date
            $ocDate = new DateTime($operatingCentreOorDate);
            if ($ocDate > $maximumDate) {
                $maximumDate = $ocDate;
            }
        }

        // If all the operating centres are 'Not applicable' then the overall OOR date = 'Not applicable'
        if ($maximumDate === null) {
            return self::NOT_APPLICABLE;
        }

        // Otherwise = <maximum date> = 21 days
        return $maximumDate->modify('+21 days');
    }

    /**
     * Calculate the Out of Representation date for an ApplicationOperatingCentre
     * If a date can be calcuated this will return a string date (YYYY-MM-DD)
     * If a date cannot be calculated it will return a string of either self::NOT_APPLICABLE or self::UNKNOWN
     *
     * @param \Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre $aoc
     *
     * @return string date|self::NOT_APPLICABLE|self::UNKNOWN
     */
    private function calcOperatingCentreOutOfRepresentationDate(ApplicationOperatingCentre $aoc)
    {
        // For added operating centres that are linked to a schedule 4
        // where there has been no increase to the vehicles as compared with the donor licence then
        if ($aoc->getAction() === 'A' && $aoc->getS4()) {
            $donorOperatingCentres = $aoc->getS4()->getLicence()->getOperatingCentres();
            /* @var $donorOperatingCentre \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre */
            foreach ($donorOperatingCentres as $donorOperatingCentre) {
                if ($donorOperatingCentre->getOperatingCentre() === $aoc->getOperatingCentre()) {
                    if ($aoc->getNoOfVehiclesRequired() <= $donorOperatingCentre->getNoOfVehiclesRequired()) {
                        return self::NOT_APPLICABLE;
                    }
                }
            }
        }

        // For updated operating centres, if there has been no increase to the vehicles
        if ($aoc->getAction() === 'U') {
            $licenceOperatingCentres = $this->getLicence()->getOperatingCentres();
            /* @var $licenceOperatingCentre \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre */
            foreach ($licenceOperatingCentres as $licenceOperatingCentre) {
                if ($licenceOperatingCentre->getOperatingCentre() === $aoc->getOperatingCentre()) {
                    if ($aoc->getNoOfVehiclesRequired() <=  $licenceOperatingCentre->getNoOfVehiclesRequired()) {
                        return self::NOT_APPLICABLE;
                    }
                }
            }
        }

        // If there is an advertisement date then
        if ($aoc->getAdPlacedDate()) {
            return $aoc->getAdPlacedDate();
        }

        // If the advertisement date is missing then
        return self::UNKNOWN;
    }

    /**
     * Get the Out Of Opposition Date
     *
     * @return DateTime|string DateTime if it can be calculated, otherwise const UNKNOWN
     */
    public function getOutOfOppositionDate()
    {
        // It is a PSV variation;
        if ($this->isPsv() && $this->isVariation()) {
            return self::NOT_APPLICABLE;
        }

        // if not publishable NOT APPLICABLE
        if (!$this->isPublishable()) {
            return self::NOT_APPLICABLE;
        }

        // if S41 AND true AND approved AND without licence upgrade then NA
        if ($this->hasApprovedTrueS4() && !$this->isRealUpgrade()) {
            return self::NOT_APPLICABLE;
        }

        if ($this->isGoods() && $this->isVariation()) {
            // It is a goods variation and 0 operating centres have been added; AND
            // It is a goods variation and 0 operating centres have been updated with an increase
            // of vehicles or trailers
            if (
                $this->getOperatingCentresAdded()->count() === 0 &&
                !$this->hasIncreaseInOperatingCentre() &&
                !$this->isRealUpgrade()
            ) {
                return self::NOT_APPLICABLE;
            }
        }

        /** @var PublicationEntity $latestPublication */
        $latestPublication = $this->getLatestPublication();

        if (!empty($latestPublication)) {
            $oooDate = new DateTime($latestPublication->getPubDate());
            $oooDate->modify('+21 days');

            return $oooDate;
        }

        return self::UNKNOWN;
    }

    /**
     * Method to return ooo as strings. Used in submission tables
     * @param string $format
     * @return string
     */
    public function getOutOfOppositionDateAsString()
    {
        $ooo = $this->getOutOfOppositionDate();
        return $this->formatDateToString($ooo);
    }

    /**
     * Method to return oor as strings. Used in submission tables
     * @param string $format
     * @return string
     */
    public function getOutOfRepresentationDateAsString()
    {
        $oor = $this->getOutOfRepresentationDate();
        return $this->formatDateToString($oor);
    }

    /**
     * @param $date
     * @param string $format
     * @return string
     */
    private function formatDateToString($date, $format = self::DATE_FORMAT)
    {
        if (is_string($date)) {
            return $date;
        }
        if ($date instanceOf \DateTime) {
            return $date->format($format);
        }
        return '';
    }

    /**
     * Get a collection of Application Operating Centres that have been added
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOperatingCentresAdded()
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq('action', 'A'));

        return $this->getOperatingCentres()->matching($criteria);
    }

    /**
     * Gets the latest publication for an application. (used to calculate OOO date)
     *
     * @return PublicationEntity|null
     */
    private function getLatestPublication()
    {
         $publicationSections = [
            \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::APP_NEW_SECTION,
            \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::VAR_NEW_SECTION,
            \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::SCHEDULE_4_NEW,
            \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::SCHEDULE_4_UNTRUE,
            \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::SCHEDULE_1_NI_NEW,
            \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::SCHEDULE_1_NI_UNTRUE,
        ];

        $latestPublication = null;
        foreach ($this->getPublicationLinks() as $publicationLink) {
            /* @var $publicationLink \Dvsa\Olcs\Api\Entity\Publication\PublicationLink */
            if (!in_array($publicationLink->getPublicationSection()->getId(), $publicationSections)) {
                continue;
            }
            /** @var PublicationEntity $latestPublication */
            if ($latestPublication === null) {
                $latestPublication = $publicationLink->getPublication();
            } elseif (
                new \DateTime($publicationLink->getPublication()->getPubDate()) >
                new \DateTime($latestPublication->getPubDate())
                ) {
                $latestPublication = $publicationLink->getPublication();
            }
        }

        return $latestPublication;
    }

    public function getActiveVehiclesCount()
    {
        return $this->getActiveLicenceVehicles()->count();
    }

    /**
     * @return array
     */
    public function getActiveS4s()
    {
        $activeS4s = [];

        /** @var S4 $s4 */
        foreach ($this->getS4s() as $s4) {
            if ($s4->getOutcome() === null) {
                $activeS4s[] = $s4;
            } elseif ($s4->getOutcome()->getId() === S4::STATUS_APPROVED) {
                $activeS4s[] = $s4;
            }
        }

        return $activeS4s;
    }

    /**
     * Does this application have an active S4 attached
     *
     * @return bool
     */
    public function hasActiveS4()
    {
        return count($this->getActiveS4s()) > 0;
    }

    /**
     * Does this application have any approved true S4s
     *
     * @return bool
     */
    public function hasApprovedTrueS4()
    {
        /* @var $s4 S4 */
        foreach ($this->getS4s() as $s4) {
            if ($s4->getOutcome() && $s4->getOutcome()->getId() === S4::STATUS_APPROVED && $s4->getIsTrueS4() === 'Y') {
                return true;
            }
        }

        return false;
    }

    public function canHaveCommunityLicences()
    {
        return ($this->isStandardInternational() || ($this->isPsv() && $this->isRestricted()));
    }

    public function getDeltaAocByOc(OperatingCentre $oc)
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq('operatingCentre', $oc));

        return $this->getOperatingCentres()->matching($criteria);
    }

    public function getCategoryPrefix()
    {
        return LicenceNoGen::getCategoryPrefix($this->getGoodsOrPsv());
    }

    /**
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }

    /**
     * @param int $publicationNo
     */
    public function setPublicationNo($publicationNo)
    {
        $this->publicationNo = $publicationNo;
    }

    /**
     * @return string
     */
    public function getOooDate()
    {
        return $this->oooDate;
    }

    /**
     * @param string $oooDate
     */
    public function setOooDate($oooDate)
    {
        $this->oooDate = $oooDate;
    }

    /**
     * @return string
     */
    public function getOorDate()
    {
        return $this->oorDate;
    }

    /**
     * @param string $oorDate
     */
    public function setOorDate($oorDate)
    {
        $this->oorDate = $oorDate;
    }

    /**
     * @return boolean
     */
    public function getIsOpposed()
    {
        return $this->isOpposed;
    }

    /**
     * @param boolean $isOpposed
     */
    public function setIsOpposed($isOpposed)
    {
        $this->isOpposed = $isOpposed;
    }

    /**
     * @return string
     */
    public function getPublishedDate()
    {
        return $this->publishedDate;
    }

    /**
     * @param string $publishedDate
     */
    public function setPublishedDate($publishedDate)
    {
        $this->publishedDate = $publishedDate;
    }

    /**
     * Determine and set the latest publication number
     * @return mixed
     */
    public function determinePublicationNo()
    {
        /** @var PublicationEntity $latestPublication */
        $latestPublication = $this->getLatestPublication();

        if ($latestPublication instanceof PublicationEntity) {

            return $latestPublication->getPublicationNo();
        }

        return null;
    }

    /**
     * Determine and set the latest publication number
     * @return mixed
     */
    public function determinePublishedDate()
    {
        /** @var PublicationEntity $latestPublication */
        $latestPublication = $this->getLatestPublication();

        if ($latestPublication instanceof PublicationEntity) {
            return $latestPublication->getPubDate();
        }

        return null;
    }

    /**
     * Has this application received any opposition
     * @return bool
     */
    public function hasOpposition()
    {
        /** @var CasesEntity $case */
        foreach ($this->getCases() as $case) {
            if (count($case->getOppositions()) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * For an application, get the organisation's other licences (explicity
     * excludes the current application's licence)
     *
     * @note different from AbstractApplication::getOtherLicences() which is,
     * erm, something else entirely
     *
     * @return array Licence[]
     */
    public function getOtherActiveLicencesForOrganisation()
    {
        if ($this->getLicence() && $this->getLicence()->getOrganisation()) {

            $licences = $this->getLicence()->getOrganisation()->getActiveLicences();

            if (empty($licences)) {
                return [];
            }

            $filtered = array_filter(
                $licences->toArray(),
                function ($licence) {
                    return $licence->getId() !== $this->getLicence()->getId();
                }
            );

            return array_values($filtered);
        }
    }

    public function getTrafficArea()
    {
        return $this->getLicence()->getTrafficArea();
    }

    public function getOperatingCentresNetDelta()
    {
        $delta = 0;

        if (!empty($this->getOperatingCentres())) {
            foreach ($this->getOperatingCentres() as $aoc) {
                switch ($aoc->getAction()) {
                    case 'A':
                        $delta++;
                        break;
                    case 'D':
                        $delta--;
                        break;
                }
            }
        }

        return $delta;
    }

    /**
     * Set the target completion date to +9 weeks from received date
     * @return this
     */
    public function setTargetCompletionDateFromReceivedDate()
    {
        $received = $this->getReceivedDate();
        $target = clone $received;
        $target->modify(self::TARGET_COMPLETION_TIME);
        $this->setTargetCompletionDate($target);
        return $this;
    }

    public function allowFeePayments()
    {
        if (in_array(
            $this->getStatus()->getId(),
            [
                self::APPLICATION_STATUS_REFUSED,
                self::APPLICATION_STATUS_WITHDRAWN,
                self::APPLICATION_STATUS_NOT_TAKEN_UP,
            ]
        )) {
            return false;
        }

        return $this->getLicence()->allowFeePayments();
    }

    public function hasAuthChanged()
    {
        if ($this->isNew()) {
            return false;
        }

        $comparisons = [
            'TotAuthVehicles',
            'TotAuthTrailers',
        ];

        foreach ($comparisons as $comparison) {
            if ((int)$this->{'get' . $comparison}() !== (int)$this->getLicence()->{'get' . $comparison}()) {
                return true;
            }
        }

        return false;
    }

    public function isUnderConsideration()
    {
        return !is_null($this->getStatus())
            && $this->getStatus()->getId() === self::APPLICATION_STATUS_UNDER_CONSIDERATION;
    }

    /**
     * Get the Shortcode version of a licence type
     *
     * @return string|null if licence type is not set or shortcode does not exist
     */
    public function getLicenceTypeShortCode()
    {
        $shortCodes = [
            'ltyp_r' => 'R',
            'ltyp_si' => 'SI',
            'ltyp_sn' => 'SN',
            'ltyp_sr' => 'SR',
        ];

        if ($this->getLicenceType() === null || !isset($shortCodes[$this->getLicenceType()->getId()])) {
            return null;
        }

        return $shortCodes[$this->getLicenceType()->getId()];
    }

    public function getContextValue()
    {
        return $this->getLicence()->getLicNo();
    }

    /**
     * Get a list of open tasks attached to the application, optionally filtered by category, sub category
     *
     * @param int $categoryId    Category ID
     * @param int $subCategoryId Sub category ID, null means all
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOpenTasksForCategory($categoryId, $subCategoryId = null)
    {
        // use Criteria to retrieve open tasks
        $criteria = Criteria::create()->where(Criteria::expr()->eq('isClosed', 'N'));
        $openTasks = $this->getTasks()->matching($criteria);

        $tasks = new \Doctrine\Common\Collections\ArrayCollection();

        // iterate to get tasks of category, subcategory
        // NB reason to iterate is crtieria should only be used with scalar values
        foreach ($openTasks as $task) {
            if ($task->getCategory()->getId() !== $categoryId) {
                continue;
            }
            if ($subCategoryId !== null && $task->getSubCategory()->getId() !== $subCategoryId) {
                continue;
            }
            $tasks->add($task);
        }

        return $tasks;
    }

    /**
     * Is this application publishable
     *
     * @return boolean
     */
    public function isPublishable()
    {
        if ($this->isNew()) {
            // It is a new Goods or PSV application
            // Excluding PSV special restricted
            if ($this->isPsv() && $this->isSpecialRestricted()) {
                return false;
            }

            return true;
        } else {
            // It is a Goods or PSV variation
            // An operating centre has been added;
            if ($this->hasNewOperatingCentre()) {
                return true;
            }

            // An operating centre has been updated and either the OC vehicles or trailers have increased;
            if ($this->hasIncreaseInOperatingCentre()) {
                return true;
            }

            // A major upgrade has taken place
            // (ie restricted to standard national or restricted to standard international)
            if ($this->isRealUpgrade()) {
                return true;
            }

            return false;
        }
    }

    /**
     * Is the PSV Size set to Small
     *
     * @return bool
     */
    public function isPsvVehicleSizeSmall()
    {
        return !empty($this->getPsvWhichVehicleSizes()) &&
            $this->getPsvWhichVehicleSizes()->getId() === self::PSV_VEHICLE_SIZE_SMALL;
    }

    /**
     * Is the PSV Size set to Medium/Large
     *
     * @return bool
     */
    public function isPsvVehicleSizeMediumLarge()
    {
        return !empty($this->getPsvWhichVehicleSizes()) &&
            $this->getPsvWhichVehicleSizes()->getId() === self::PSV_VEHICLE_SIZE_MEDIUM_LARGE;
    }

    /**
     * Is the PSV Size set to Both
     *
     * @return bool
     */
    public function isPsvVehicleSizeBoth()
    {
        return !empty($this->getPsvWhichVehicleSizes()) &&
            $this->getPsvWhichVehicleSizes()->getId() === self::PSV_VEHICLE_SIZE_BOTH;
    }

    /**
     * @return FeeEntity|null
     */
    public function getLatestOutstandingApplicationFee()
    {
        if ($this->isVariation()) {
            $feeTypeFeeTypeId = FeeTypeEntity::FEE_TYPE_VAR;
        } else {
            $feeTypeFeeTypeId = FeeTypeEntity::FEE_TYPE_APP;
        }

        $criteria = Criteria::create()
            ->orderBy(['invoicedDate' => Criteria::DESC]);

        foreach ($this->getFees()->matching($criteria) as $fee) {
            if ($fee->isOutstanding()
                && $fee->getFeeType()->getFeeType()->getId() === $feeTypeFeeTypeId) {
                return $fee;
            }
        }
    }

    /**
     * @return FeeEntity|null
     */
    public function getLatestOutstandingInterimFee()
    {
        $criteria = Criteria::create()
            ->orderBy(['invoicedDate' => Criteria::DESC]);

        foreach ($this->getFees()->matching($criteria) as $fee) {
            if ($fee->isOutstanding()
                && $fee->getFeeType()->getFeeType()->getId() === FeeTypeEntity::FEE_TYPE_GRANTINT) {
                return $fee;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasOutstandingGrantFee()
    {
        return !empty($this->getOutstandingGrantFees());
    }

    /**
     * @return array
     */
    public function getOutstandingGrantFees()
    {
        $grantFees = [];

        foreach ($this->getFees() as $fee) {
            if ($fee->isGrantFee() && $fee->isOutstanding()) {
                $grantFees[$fee->getId()] = $fee;
            }
        }

        return $grantFees;
    }

    /**
     * Use the applied_via value to determine whether an application was
     * created internally or via selfserve
     *
     * @return bool|null
     */
    public function createdInternally()
    {
        if ($this->getAppliedVia()) {
            return !($this->getAppliedVia()->getId() === self::APPLIED_VIA_SELFSERVE);
        }
    }

    /**
     * @return bool whether the application already has a fee (that is not cancelled)
     */
    public function hasApplicationFee()
    {
        foreach ($this->getFees() as $fee) {
            if (
                ($fee->isNewApplicationFee() || $fee->isVariationFee())
                && !$fee->isCancelled()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the application reference number.
     *
     * @return string eg OB12345/12
     */
    public function getApplicationReference()
    {
        if ($this->getLicence() && !empty($this->getLicence()->getLicNo())) {
            $applicationReference = $this->getLicence()->getLicNo().'/'.$this->getId();
        } else {
            $applicationReference = $this->getId();
        }

        return $applicationReference;
    }

    /**
     * Calculated values to be added to a bundle
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'applicationReference' => $this->getApplicationReference()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelatedOrganisation()
    {
        return $this->getLicence()->getOrganisation();
    }
}
