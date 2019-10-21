<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceNoGen as LicenceNoGenEntity;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\TradingName as TradingNameEntity;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;

/**
 * Licence Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="licence",
 *    indexes={
 *        @ORM\Index(name="ix_licence_enforcement_area_id", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="ix_licence_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_licence_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_licence_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_licence_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_licence_status", columns={"status"}),
 *        @ORM\Index(name="ix_licence_tachograph_ins", columns={"tachograph_ins"}),
 *        @ORM\Index(name="ix_licence_correspondence_cd_id", columns={"correspondence_cd_id"}),
 *        @ORM\Index(name="ix_licence_establishment_cd_id", columns={"establishment_cd_id"}),
 *        @ORM\Index(name="ix_licence_transport_consultant_cd_id", columns={"transport_consultant_cd_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_licence_lic_no", columns={"lic_no"}),
 *        @ORM\UniqueConstraint(name="uk_licence_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Licence extends AbstractLicence implements ContextProviderInterface, OrganisationProviderInterface
{
    use LicenceStatusAwareTrait;

    const ERROR_CANT_BE_SR = 'LIC-TOL-1';
    const ERROR_REQUIRES_VARIATION = 'LIC-REQ-VAR';
    const ERROR_SAFETY_REQUIRES_TACHO_NAME = 'LIC-SAFE-TACH-1';

    const ERROR_TRANSFER_TOT_AUTH = 'LIC_TRAN_1';
    const ERROR_TRANSFER_OVERLAP_ONE = 'LIC_TRAN_2';
    const ERROR_TRANSFER_OVERLAP_MANY = 'LIC_TRAN_3';

    const LICENCE_CATEGORY_GOODS_VEHICLE = 'lcat_gv';
    const LICENCE_CATEGORY_PSV = 'lcat_psv';

    const LICENCE_TYPE_RESTRICTED = 'ltyp_r';
    const LICENCE_TYPE_STANDARD_INTERNATIONAL = 'ltyp_si';
    const LICENCE_TYPE_STANDARD_NATIONAL = 'ltyp_sn';
    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';

    const LICENCE_STATUS_UNDER_CONSIDERATION = 'lsts_consideration';
    const LICENCE_STATUS_NOT_SUBMITTED = 'lsts_not_submitted';
    const LICENCE_STATUS_SUSPENDED = 'lsts_suspended';
    const LICENCE_STATUS_VALID = 'lsts_valid';
    const LICENCE_STATUS_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_GRANTED = 'lsts_granted';
    const LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION = 'lsts_surr_consideration';
    const LICENCE_STATUS_SURRENDERED = 'lsts_surrendered';
    const LICENCE_STATUS_WITHDRAWN = 'lsts_withdrawn';
    const LICENCE_STATUS_REFUSED = 'lsts_refused';
    const LICENCE_STATUS_REVOKED = 'lsts_revoked';
    const LICENCE_STATUS_NOT_TAKEN_UP = 'lsts_ntu';
    const LICENCE_STATUS_TERMINATED = 'lsts_terminated';
    const LICENCE_STATUS_CONTINUATION_NOT_SOUGHT = 'lsts_cns';
    const LICENCE_STATUS_UNLICENSED = 'lsts_unlicenced'; // note, refdata misspelled
    const LICENCE_STATUS_CANCELLED = 'lsts_cancelled';

    const TACH_EXT = 'tach_external';
    const TACH_INT = 'tach_internal';
    const TACH_NA = 'tach_na';

    /**
     * Licence constructor
     *
     * @param Organisation $organisation licence organisation
     * @param RefData      $status       licence status
     */
    public function __construct(Organisation $organisation, RefData $status)
    {
        parent::__construct();

        $this->setOrganisation($organisation);
        $this->setStatus($status);
    }

    /**
     * At the moment a licence can only be special restricted, if it is already special restricted.
     * It seems pointless putting this logic in here, however it is a business rule, and if the business rule changes,
     * then the web app shouldn't need changing
     *
     * @return bool
     */
    public function canBecomeSpecialRestricted()
    {
        if ($this->getGoodsOrPsv() == null && $this->getLicenceType() == null) {
            return true;
        }

        return ($this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV
            && $this->getLicenceType()->getId() === self::LICENCE_TYPE_SPECIAL_RESTRICTED
        );
    }

    /**
     * Check whether the licence can make an ECMT application (in some cases excluding checks on the current app)
     *
     * @param IrhpPermitStock            $stock
     * @param EcmtPermitApplication|null $exclude
     *
     * @return bool
     */
    public function canMakeEcmtApplication(IrhpPermitStock $stock, ?EcmtPermitApplication $exclude = null): bool
    {
        $activeApplication = $this->getActiveEcmtApplicationForStock($stock, $exclude);
        return !$activeApplication instanceof EcmtPermitApplication && $this->isEligibleForPermits();
    }

    /**
     * Get an active ECMT permit application for the stock (if it exists)
     *
     * @param IrhpPermitStock            $stock
     * @param EcmtPermitApplication|null $exclude
     *
     * @return EcmtPermitApplication|null
     */
    public function getActiveEcmtApplicationForStock(
        IrhpPermitStock $stock,
        ?EcmtPermitApplication $exclude = null
    ): ?EcmtPermitApplication {
        $emctApplications = $this->getEcmtApplications();
        if ($emctApplications === null) {
            return null;
        }

        /** @var EcmtPermitApplication $application */
        foreach ($emctApplications as $application) {
            if ($exclude instanceof EcmtPermitApplication && $application->getId() === $exclude->getId()) {
                return null;
            }

            if ($stock instanceof IrhpPermitStock
                && $application->getAssociatedStock()->getId() === $stock->getId()
                && $application->isActive()
            ) {
                return $application;
            }
        }

        return null;
    }

    /**
     * Check whether the licence can make an IRHP application of specific type
     *
     * @param IrhpPermitStock      $stock    type to be checked
     * @param IrhpApplication|null $exclude application to exclude
     *
     * @return bool
     */
    public function canMakeIrhpApplication(IrhpPermitStock $stock, ?IrhpApplication $exclude = null): bool
    {
        $activeApplication = $this->getActiveIrhpApplication($stock, $exclude);
        return !$activeApplication instanceof IrhpApplication && $this->isEligibleForPermits();
    }

    /**
     * If the licence has an active IRHP application we return it, else null is returned
     *
     * @param IrhpPermitStock       $stock    type to be checked
     * @param IrhpApplication|null  $exclude  application to exclude
     *
     * @return IrhpApplication|null
     */
    public function getActiveIrhpApplication(
        IrhpPermitStock $stock,
        ?IrhpApplication $exclude = null
    ): ?IrhpApplication {
        $criteria = Criteria::create();
        $criteria->andWhere(
            Criteria::expr()->in(
                'status',
                IrhpInterface::ACTIVE_STATUSES
            )
        );

        $criteria->andWhere(
            Criteria::expr()->eq(
                'irhpPermitType',
                $stock->getIrhpPermitType()
            )
        );

        $activeApplications = $this->getIrhpApplications()->matching($criteria);

        if ($activeApplications->isEmpty()) {
            return null;
        }

        $stockId = $stock->getId();

        /** @var IrhpApplication $application */
        foreach ($activeApplications as $application) {
            if ($exclude instanceof IrhpApplication && $exclude->getId() === $application->getId()) {
                continue;
            }

            // for permit types other than multilateral and bilateral, we can do more specific checks
            if (!$application->isMultiStock() && $application->getAssociatedStock()->getId() !== $stockId) {
                // only consider the stock requested
                continue;
            }

            return $application;
        }

        return null;
    }

    /**
     * If this licence has an active permit application for a given stock, return it
     *
     * @param IrhpPermitStock    $stock   permit stock
     * @param IrhpInterface|null $exclude excluded application
     *
     * @return IrhpInterface|null
     */
    public function getActivePermitApplicationForStock(
        IrhpPermitStock $stock,
        ?IrhpInterface $exclude = null
    ): ?IrhpInterface {
        if ($stock->getIrhpPermitType()->isEcmtAnnual()) {
            return $this->getActiveEcmtApplicationForStock($stock, $exclude);
        }

        return $this->getActiveIrhpApplication($stock, $exclude);
    }

    /**
     * Gets the latest Bus Reg variation number, based on the supplied regNo
     *
     * @param string $regNo       bus registration number
     * @param array  $notInStatus statuses to ignore
     *
     * @return BusReg|null
     */
    public function getLatestBusVariation(
        $regNo,
        array $notInStatus = [
        BusReg::STATUS_REFUSED,
        BusReg::STATUS_WITHDRAWN,
        BusReg::STATUS_EXPIRED
        ]
    ) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('regNo', $regNo))
            ->orderBy(array('variationNo' => Criteria::DESC))
            ->setMaxResults(1);

        if (!empty($notInStatus)) {
            $criteria->andWhere(Criteria::expr()->notIn('status', $notInStatus));
        }

        $matchedBusReg = $this->getBusRegs()->matching($criteria);

        if (!$matchedBusReg->isEmpty()) {
            return $matchedBusReg->current();
        }

        return null;
    }

    /**
     * Gets the latest Bus Reg route number for the licence
     *
     * @return int
     */
    public function getLatestBusRouteNo()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('licence', $this))
            ->orderBy(array('routeNo' => Criteria::DESC))
            ->setMaxResults(1);

        return !empty($this->getBusRegs()->matching($criteria)->current())
            ? $this->getBusRegs()->matching($criteria)->current()->getRouteNo() : 0;
    }

    /**
     * Updates total community licences
     *
     * @param int $totalCount total count
     *
     * @return void
     */
    public function updateTotalCommunityLicences($totalCount)
    {
        $this->totCommunityLicences = $totalCount;
    }

    /**
     * update safety details
     *
     * @param int     $safetyInsVehicles safetyInsVehicles
     * @param int     $safetyInsTrailers safetyInsTrailers
     * @param RefData $tachographIns     tachographIns
     * @param string  $tachographInsName tachographInsName
     * @param string  $safetyInsVaries   safetyInsVaries
     *
     * @return void
     * @throws ValidationException
     */
    public function updateSafetyDetails(
        $safetyInsVehicles,
        $safetyInsTrailers,
        $tachographIns,
        $tachographInsName,
        $safetyInsVaries
    ) {
        if ($tachographIns !== null && $tachographIns == self::TACH_EXT && empty($tachographInsName)) {
            throw new ValidationException(
                [
                    'tachographInsName' => [
                        [
                            self::ERROR_SAFETY_REQUIRES_TACHO_NAME => 'You must specify a tachograph inspector name'
                        ]
                    ]
                ]
            );
        }

        if (empty($safetyInsVehicles)) {
            $safetyInsVehicles = null;
        }

        $this->setSafetyInsVehicles($safetyInsVehicles);

        $this->setSafetyInsTrailers($safetyInsTrailers);

        $this->setTachographIns($tachographIns);

        $this->setTachographInsName($tachographInsName);

        $this->setSafetyInsVaries($safetyInsVaries);
    }

    /**
     * Get active community licences
     *
     * @return CollectionInterface
     */
    public function getActiveCommunityLicences()
    {
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->in(
                    'status',
                    [
                        CommunityLicEntity::STATUS_PENDING,
                        CommunityLicEntity::STATUS_ACTIVE,
                        CommunityLicEntity::STATUS_SUSPENDED,
                    ]
                )
            )
            ->andWhere(
                Criteria::expr()->neq('issueNo', 0)
            );

        return $this->getCommunityLics()->matching($criteria);
    }

    /**
     * Get Active variation for this licence
     *
     * @return ArrayCollection
     */
    public function getActiveVariations()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isVariation', true))
            ->andWhere(
                Criteria::expr()->in(
                    'status',
                    [
                        Application::APPLICATION_STATUS_UNDER_CONSIDERATION
                    ]
                )
            );

        return $this->getApplications()->matching($criteria);
    }

    /**
     * Get calculated bundle values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'niFlag' => $this->getNiFlag()
        ];
    }

    /**
     * Get calculated values
     *
     * @return array
     */
    public function getCalculatedValues()
    {
        return $this->getCalculatedBundleValues();
    }

    /**
     * Get serial number prefix (UKGB or UKNI)
     *
     * @return string
     */
    public function getSerialNoPrefixFromTrafficArea()
    {
        $trafficArea = $this->getTrafficArea();

        if ($trafficArea && $trafficArea->getIsNi()) {
            return CommunityLicEntity::PREFIX_NI;
        }

        return CommunityLicEntity::PREFIX_GB;
    }

    /**
     * Get remaining spaces for new vehicles
     *
     * @return int
     */
    public function getRemainingSpaces()
    {
        return $this->getTotAuthVehicles() - $this->getActiveVehiclesCount();
    }

    /**
     * Get number of active vehicles
     *
     * @return int
     */
    public function getActiveVehiclesCount()
    {
        return $this->getActiveVehicles()->count();
    }

    /**
     * Get remaining spaces for PSV
     *
     * @return int
     */
    public function getRemainingSpacesPsv()
    {
        return $this->getTotAuthVehicles() - $this->getPsvDiscsNotCeasedCount();
    }

    /**
     * Get count of PSV discs not ceased
     *
     * @return int
     */
    public function getPsvDiscsNotCeasedCount()
    {
        return $this->getPsvDiscsNotCeased()->count();
    }

    /**
     * Returns list of active vehicles
     *
     * @param bool $checkSpecified When true, only return vehicles with a specified date
     *
     * @return ArrayCollection
     */
    public function getActiveVehicles($checkSpecified = true)
    {
        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->isNull('removalDate'));
        $criteria->andWhere($criteria->expr()->eq('interimApplication', null));

        if ($checkSpecified) {
            $criteria->andWhere($criteria->expr()->neq('specifiedDate', null));
        }

        return $this->getLicenceVehicles()->matching($criteria);
    }

    /**
     * has community licence office copy
     *
     * @param array $ids ids
     *
     * @return bool
     */
    public function hasCommunityLicenceOfficeCopy($ids)
    {
        $hasOfficeCopy = false;

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('issueNo', 0))
            ->andWhere(
                Criteria::expr()->in(
                    'status',
                    [
                        CommunityLicEntity::STATUS_PENDING,
                        CommunityLicEntity::STATUS_ACTIVE,
                        CommunityLicEntity::STATUS_WITHDRAWN,
                        CommunityLicEntity::STATUS_SUSPENDED
                    ]
                )
            )
            ->setMaxResults(1);

        $officeCopy = $this->getCommunityLics()->matching($criteria)->current();
        if ($officeCopy) {
            $officeCopyId = $officeCopy->getId();
            if (in_array($officeCopyId, $ids)) {
                $hasOfficeCopy = true;
            }
        }
        return $hasOfficeCopy;
    }

    /**
     * Get other active licences
     *
     * @return CollectionInterface
     */
    public function getOtherActiveLicences()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->in('status', $this->getLicenceStatusesStrictlyActive())
        );
        $criteria->andWhere(
            $criteria->expr()->eq('goodsOrPsv', $this->getGoodsOrPsv())
        );
        $criteria->andWhere(
            $criteria->expr()->neq('id', $this->getId())
        );

        /** @var ArrayCollection $otherActiveLicences */
        $otherActiveLicences = $this->getOrganisation()->getLicences()->matching($criteria);

        // goods_or_psv can be null
        if (!empty($this->getGoodsOrPsv()) &&
            ($this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV)
        ) {

            /** @var Licence $otherActiveLicence */
            foreach ($otherActiveLicences as $otherActiveLicence) {
                $licenceType = $otherActiveLicence->getLicenceType();
                if ($licenceType !== null && $licenceType->getId() === self::LICENCE_TYPE_SPECIAL_RESTRICTED) {
                    $otherActiveLicences->removeElement($otherActiveLicence);
                }
            }
        }

        return $otherActiveLicences;
    }

    /**
     * Check whether licence has conditions and undertakings that aren't fulfilled
     *
     * @return bool
     */
    public function hasApprovedUnfulfilledConditions()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq('isDraft', 0)
        );
        $criteria->andWhere(
            $criteria->expr()->eq('isFulfilled', 0)
        );

        return ($this->getConditionUndertakings()->matching($criteria)->count() > 0);
    }

    /**
     * Get grouped conditions and undertakings
     *
     * @return array
     */
    public function getGroupedConditionsUndertakings()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq('isDraft', 'N')
        );
        $criteria->andWhere(
            $criteria->expr()->eq('isFulfilled', 'N')
        );

        $conditionsUndertakings = $this->getConditionUndertakings()->matching($criteria);
        $licenceConditionsUndertakings = [];
        $ocConditionsUndertakings = [];
        $map = [
            ConditionUndertaking::TYPE_CONDITION => 'conditions',
            ConditionUndertaking::TYPE_UNDERTAKING => 'undertakings',
        ];

        /** @var ConditionUndertaking $cu */
        foreach ($conditionsUndertakings as &$cu) {
            $conditionType = $map[$cu->getConditionType()->getId()];
            if ($cu->getAttachedTo()->getId() === ConditionUndertaking::ATTACHED_TO_LICENCE) {
                $licenceConditionsUndertakings[$conditionType][] = [
                    'notes' => $cu->getNotes(),
                    'createdOn' => $cu->getCreatedOn(true)
                ];
            } else {
                $ocConditionsUndertakings[$cu->getOperatingCentre()->getId()][$conditionType][] = [
                    'notes' => $cu->getNotes(),
                    'address' => [
                        'addressLine1' => $cu->getOperatingCentre()->getAddress()->getAddressLine1(),
                        'addressLine2' => $cu->getOperatingCentre()->getAddress()->getAddressLine2(),
                        'addressLine3' => $cu->getOperatingCentre()->getAddress()->getAddressLine3(),
                        'addressLine4' => $cu->getOperatingCentre()->getAddress()->getAddressLine4(),
                        'town' => $cu->getOperatingCentre()->getAddress()->getTown(),
                        'postcode' => $cu->getOperatingCentre()->getAddress()->getPostcode(),
                    ],
                    'createdOn' => $cu->getCreatedOn(true)
                ];
            }
        }

        if (isset($licenceConditionsUndertakings['conditions'])
            && count($licenceConditionsUndertakings['conditions']) > 0) {
            $this->sortConditionsUndertakings($licenceConditionsUndertakings['conditions']);
        }

        if (isset($licenceConditionsUndertakings['undertakings'])
            && count($licenceConditionsUndertakings['undertakings']) > 0) {
            $this->sortConditionsUndertakings($licenceConditionsUndertakings['undertakings']);
        }

        foreach ($ocConditionsUndertakings as &$oc) {
            if (isset($oc['conditions']) && count($oc['conditions']) > 0) {
                $this->sortConditionsUndertakings($oc['conditions']);
            }

            if (isset($oc['undertakings']) && count($oc['undertakings']) > 0) {
                $this->sortConditionsUndertakings($oc['undertakings']);
            }
        }

        return [
            'licence' => $licenceConditionsUndertakings,
            'operatingCentres' => $ocConditionsUndertakings,
        ];
    }

    /**
     * Sort conditions and undertakings
     *
     * @param array &$conditionsUndertakings conditions and undertakings
     *
     * @return void
     */
    private function sortConditionsUndertakings(&$conditionsUndertakings)
    {
        usort($conditionsUndertakings, function ($a, $b) {
            if ($a['createdOn'] == $b['createdOn']) {
                return 0;
            }

            return ($a['createdOn'] > $b['createdOn']) ? +1 : -1;
        });
    }

    /**
     * Is this a valid standard international goods licence
     *
     * @return bool
     */
    public function isValidSiGoods()
    {
        return $this->isValidGoods() && $this->isStandardInternational();
    }

    /**
     * Whether the licence is active goods (sometimes also described as valid)
     *
     * @return bool
     */
    public function isValidGoods()
    {
        return $this->isValid() && $this->isGoods();
    }

    /**
     * Whether the licence is active (sometimes also described as valid)
     *
     * @return bool
     */
    public function isValid()
    {
        return in_array($this->status->getId(), $this->getLicenceStatusesStrictlyActive());
    }

    /**
     * Returns whether the licence is a goods licence
     *
     * @return boolean|null
     */
    public function isGoods()
    {
        if (!empty($this->getGoodsOrPsv())) {
            return $this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_GOODS_VEHICLE;
        }
    }

    /**
     * Returns whether the licence is a PSV licence
     *
     * @return boolean|null
     */
    public function isPsv()
    {
        if (!empty($this->getGoodsOrPsv())) {
            return $this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV;
        }
    }

    /**
     * Returns whether the licence is special restricted
     *
     * @return boolean|null
     */
    public function isSpecialRestricted()
    {
        if (!empty($this->getLicenceType())) {
            return $this->getLicenceType()->getId() === self::LICENCE_TYPE_SPECIAL_RESTRICTED;
        }
    }

    /**
     * Returns whether the licence is restricted
     *
     * @return boolean|null
     */
    public function isRestricted()
    {
        if (!empty($this->getLicenceType())) {
            return $this->getLicenceType()->getId() === self::LICENCE_TYPE_RESTRICTED;
        }
    }

    /**
     * Returns whether the licence is standard international
     *
     * @return boolean|null
     */
    public function isStandardInternational()
    {
        if (!empty($this->getLicenceType())) {
            return $this->getLicenceType()->getId() === self::LICENCE_TYPE_STANDARD_INTERNATIONAL;
        }
    }

    /**
     * Returns whether the licence is standard national
     *
     * @return boolean|null
     */
    public function isStandardNational()
    {
        if (!empty($this->getLicenceType())) {
            return $this->getLicenceType()->getId() === self::LICENCE_TYPE_STANDARD_NATIONAL;
        }
    }

    /**
     * Is this licence eligible for permits
     *
     * @return bool
     */
    public function isEligibleForPermits()
    {
        return $this->isValidGoods() &&
            ($this->isStandardInternational() || $this->isRestricted() || $this->isStandardNational());
    }

    /**
     * Helper method to get the first trading name from a licence
     * (Sorts trading names by createdOn date then alphabetically)
     *
     * @return string
     */
    public function getTradingName()
    {
        $tradingNames = (array) $this->getTradingNames()->getIterator();

        if (empty($tradingNames)) {
            return 'None';
        }

        usort(
            $tradingNames,
            function ($a, $b) {
                if ($a->getCreatedOn() == $b->getCreatedOn()) {
                    // This *should* be an extreme edge case but there is a bug
                    // in Business Details causing trading names to have the
                    // same createdOn date. Sort alphabetically to avoid
                    // 'random' behaviour.
                    return strcasecmp($a->getName(), $b->getName());
                }
                return strtotime($a->getCreatedOn()) < strtotime($b->getCreatedOn()) ? -1 : 1;
            }
        );

        return array_shift($tradingNames)->getName();
    }

    /**
     * Helper method to get the all trading names from a licence
     * (Sorts trading names by createdOn date then alphabetically)
     *
     * @return string
     */
    public function getAllTradingNames()
    {
        $iterator = (array) $this->getTradingNames()->getIterator();

        usort(
            $iterator,
            function ($a, $b) {
                if ($a->getCreatedOn() == $b->getCreatedOn()) {
                    return strcasecmp($a->getName(), $b->getName());
                }
                return strtotime($a->getCreatedOn()) < strtotime($b->getCreatedOn()) ? -1 : 1;
            }
        );

        $data = [];
        /** @var TradingNameEntity $tradingName */
        foreach ($iterator as $tradingName) {
            $data[] = $tradingName->getName();
        }
        return $data;
    }

    /**
     * Get a count of open complaints
     *
     * @return int
     */
    public function getOpenComplaintsCount()
    {
        $count = 0;
        /** @var CasesEntity $case */
        foreach ($this->getCases() as $case) {
            /** @var \Dvsa\Olcs\Api\Entity\Cases\Complaint $complaint */
            foreach ($case->getComplaints() as $complaint) {
                if ($complaint->getIsCompliance() == 0 && $complaint->isOpen()) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Get Pi record count
     *
     * @return int
     */
    public function getPiRecordCount()
    {
        $count = 0;
        /** @var CasesEntity $case */
        foreach ($this->getCases() as $case) {
            if (!empty($case->getPublicInquiry())) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Returns open cases
     *
     * @return array
     */
    public function getOpenCases()
    {
        $allCases = (array) $this->getCases()->getIterator();
        return array_filter(
            $allCases,
            function ($case) {
                return $case->isOpen();
            }
        );
    }

    /**
     * Copies information from the application onto the licence
     *
     * @param Application $application application to copy information from
     *
     * @return void
     */
    public function copyInformationFromApplication(Application $application)
    {
        $this->setGoodsOrPsv($application->getGoodsOrPsv());

        if ($application->isVariation()) {
            $appCompletion = $application->getApplicationCompletion();

            if ($appCompletion->variationSectionUpdated('typeOfLicence')) {
                $this->setLicenceType($application->getLicenceType());
            }

            if ($appCompletion->variationSectionUpdated('operatingCentres')) {
                $this->setTotAuthTrailers($application->getTotAuthTrailers());
                $this->setTotAuthVehicles($application->getTotAuthVehicles());
            }

            return;
        }

        //application isn't a variation, we don't need to do conditional checks
        $this->setLicenceType($application->getLicenceType());
        $this->setTotAuthTrailers($application->getTotAuthTrailers());
        $this->setTotAuthVehicles($application->getTotAuthVehicles());
    }

    /**
     * Gets a list of operating centres for the licence
     *
     * @return array
     */
    public function getOcForInspectionRequest()
    {
        $list = [];
        $licenceOperatingCentres = $this->getOperatingCentres();
        foreach ($licenceOperatingCentres as $licenceOperatingCentre) {
            $list[] = $licenceOperatingCentre->getOperatingCentre();
        }
        return $list;
    }

    /**
     * Get PSV discs that are not ceased
     *
     * @return ArrayCollection
     */
    public function getPsvDiscsNotCeased()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('ceasedDate'));

        return $this->getPsvDiscs()->matching($criteria);
    }

    /**
     * Returns whether the licence can have community licences
     *
     * @return bool
     */
    public function canHaveCommunityLicences()
    {
        return ($this->isStandardInternational() || ($this->isPsv() && $this->isRestricted()));
    }

    /**
     * Can the licence have a variation.
     *
     * @return bool
     */
    public function canHaveVariation()
    {
        return !in_array(
            $this->getStatus()->getId(),
            [
                self::LICENCE_STATUS_REVOKED,
                self::LICENCE_STATUS_SURRENDERED,
                self::LICENCE_STATUS_TERMINATED,
                self::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT
            ]
        );
    }

    /**
     * Get category prefix (P or O)
     *
     * @return string
     */
    public function getCategoryPrefix()
    {
        return LicenceNoGenEntity::getCategoryPrefix($this->getGoodsOrPsv());
    }

    /**
     * Whether to allow fee payments (based on the licence status)
     *
     * @return bool
     */
    public function allowFeePayments()
    {
        if (in_array(
            $this->getStatus()->getId(),
            [
                self::LICENCE_STATUS_REVOKED,
                self::LICENCE_STATUS_TERMINATED,
                self::LICENCE_STATUS_SURRENDERED,
                self::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
                self::LICENCE_STATUS_REFUSED,
                self::LICENCE_STATUS_WITHDRAWN,
                self::LICENCE_STATUS_NOT_TAKEN_UP,
            ]
        )) {
            return false;
        }

        return true;
    }

    /**
     * Get Outstanding applications of status "under consideration" or "granted" and optionally "not submitted"
     *
     * @param bool $includeNotSubmitted whether to include application not submitted
     *
     * @return CollectionInterface
     */
    public function getOutstandingApplications($includeNotSubmitted = false)
    {
        $status = [
            Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Application::APPLICATION_STATUS_GRANTED
        ];
        if ($includeNotSubmitted) {
            $status[] = Application::APPLICATION_STATUS_NOT_SUBMITTED;
        }
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->in('status', $status)
            );
        return $this->getApplications()->matching($criteria);
    }

    /**
     * Return applications of a particular status
     *
     * @param array $status status
     *
     * @return CollectionInterface
     */
    public function getApplicationsByStatus($status)
    {
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->in('status', $status)
            );

        return $this->getApplications()->matching($criteria);
    }

    /**
     * Return Conditions and Undertakings that are added via Licence. Used in submissions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConditionUndertakingsAddedViaLicence()
    {
        $expr = Criteria::expr();

        $criteria = Criteria::create()
            ->where(
                $expr->andX(
                    $expr->eq('deletedDate', null),
                    $expr->in(
                        'addedVia',
                        [
                            ConditionUndertaking::ADDED_VIA_LICENCE,
                        ]
                    )
                )
            );

        return $this->getConditionUndertakings()->matching($criteria);
    }

    /**
     * Return Conditions and Undertakings those are incorrectly imported from OLBS db. It has via Application,
     * but licenceId specified instead of applicationId
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConditionUndertakingsAddedViaImport()
    {
        $expr = Criteria::expr();

        $criteria = Criteria::create()
            ->where(
                $expr->andX(
                    $expr->eq('deletedDate', null),
                    $expr->eq('application', null),
                    $expr->in(
                        'addedVia',
                        [
                            ConditionUndertaking::ADDED_VIA_APPLICATION,
                        ]
                    )
                )
            );

        return $this->getConditionUndertakings()->matching($criteria);
    }

    /**
     * Get the shortcode version of a licence type
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
            'ltyp_cbp' => 'CBP',
            'ltyp_dbp' => 'DBP',
            'ltyp_lbp' => 'LBP',
            'ltyp_sbp' => 'SBP',
        ];

        if ($this->getLicenceType() === null || !isset($shortCodes[$this->getLicenceType()->getId()])) {
            return null;
        }

        return $shortCodes[$this->getLicenceType()->getId()];
    }

    /**
     * Get context value (the lic no)
     *
     * @return string
     */
    public function getContextValue()
    {
        return $this->getLicNo();
    }

    /**
     * Determine NI ('is Northern Ireland') flag from traffic area, replaces
     * deprecated 'ni_flag' database column
     *
     * @return string 'Y'|'N'
     */
    public function getNiFlag()
    {
        $trafficArea = $this->getTrafficArea();
        if ($trafficArea && $trafficArea->getIsNi()) {
            return 'Y';
        }

        return 'N';
    }

    /**
     * Is this licence an NI licence
     *
     * @return bool
     */
    public function isNi()
    {
        return $this->getNiFlag() === 'Y';
    }

    /**
     * Returns the latest publication by type (A&D or N&P)
     *
     * @param string $type publication type
     *
     * @return PublicationEntity
     */
    public function getLatestPublicationByType($type)
    {
        $iterator = $this->getPublicationLinks()->getIterator();

        $iterator->uasort(
            function ($a, $b) {
                /** @var PublicationLinkEntity $a */
                /** @var PublicationLinkEntity $b */
                return strtotime($b->getPublication()->getPubDate()) -
                    strtotime($a->getPublication()->getPubDate());
            }
        );
        $publicationLinks = new ArrayCollection(iterator_to_array($iterator));

        /** @var PublicationLinkEntity $pLink */
        foreach ($publicationLinks as $pLink) {
            if ($pLink->getPublication()->getPubType() == $type) {
                return $pLink->getPublication();
            }
        }
    }

    /**
     * returns the publication number of the latest N&P publication featuring this licence
     *
     * @return int|null
     */
    public function determineNpNumber()
    {
        $latestNpPublication = $this->getLatestPublicationByType(PublicationEntity::PUB_TYPE_N_P);
        if ($latestNpPublication instanceof PublicationEntity) {
            return $latestNpPublication->getPublicationNo();
        }
        return null;
    }

    /**
     * Get a licence operating centre record from an operating centre record
     *
     * @param OperatingCentre $oc operating centre entity
     *
     * @return LicenceOperatingCentre|null
     */
    public function getLocByOc(OperatingCentre $oc)
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq('operatingCentre', $oc));

        $locs = $this->getOperatingCentres()->matching($criteria);

        if ($locs->isEmpty()) {
            return null;
        }

        return $locs->first();
    }

    /**
     * Get variation applications
     *
     * @return CollectionInterface
     */
    public function getVariations()
    {
        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('isVariation', true));
        return $this->getApplications()->matching($criteria);
    }

    /**
     * Get new applications
     *
     * @return ArrayCollection
     */
    public function getNewApplications()
    {
        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('isVariation', false));
        return $this->getApplications()->matching($criteria);
    }

    /**
     * Has this licence got a queued/scheduled revocation
     *
     * @return bool
     */
    public function hasQueuedRevocation()
    {
        foreach ($this->getLicenceStatusRules() as $licenceStatusRule) {
            /* @var $licenceStatusRule LicenceStatusRule */
            if ($licenceStatusRule->getLicenceStatus()->getId() === Licence::LICENCE_STATUS_REVOKED &&
                $licenceStatusRule->isQueued()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the organisation attached to the licence
     *
     * @return Organisation
     */
    public function getRelatedOrganisation()
    {
        return $this->getOrganisation();
    }

    /**
     * Get documents for the licence by category and sub category
     *
     * @param RefData $category    document category
     * @param RefData $subCategory document sub category
     *
     * @return CollectionInterface
     */
    public function getLicenceDocuments($category, $subCategory)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where($expr->eq('category', $category));
        $criteria->andWhere(
            $expr->eq('subCategory', $subCategory)
        );

        return $this->documents->matching($criteria);
    }

    /**
     * Get first application id for the new licence
     *
     * @return int|null
     */
    public function getFirstApplicationId()
    {
        $firstApplicationId = null;
        $statuses = [
            self::LICENCE_STATUS_NOT_SUBMITTED,
            self::LICENCE_STATUS_UNDER_CONSIDERATION,
            self::LICENCE_STATUS_GRANTED,
            self::LICENCE_STATUS_NOT_TAKEN_UP,
            self::LICENCE_STATUS_WITHDRAWN,
            self::LICENCE_STATUS_REFUSED
        ];
        if (in_array($this->getStatus()->getId(), $statuses)) {
            $applications = $this->getApplications();
            if ($applications->count()) {
                $firstApplicationId = $applications[0]->getId();
            }
        }

        return $firstApplicationId;
    }

    /**
     * isGoods for the application for the new licence
     *
     * @return bool
     */
    public function isGoodsApplication()
    {
        $isGoods = false;
        $statuses = [
            self::LICENCE_STATUS_NOT_SUBMITTED,
            self::LICENCE_STATUS_UNDER_CONSIDERATION,
            self::LICENCE_STATUS_GRANTED,
            self::LICENCE_STATUS_NOT_TAKEN_UP,
            self::LICENCE_STATUS_WITHDRAWN,
            self::LICENCE_STATUS_REFUSED
        ];
        if (in_array($this->getStatus()->getId(), $statuses)) {
            $applications = $this->getApplications();
            if ($applications->count()) {
                $isGoods = $applications[0]->isGoods();
            }
        }

        return $isGoods;
    }

    /**
     * Get traffic area for task allocation
     *
     * @return TrafficArea
     */
    public function getTrafficAreaForTaskAllocation()
    {
        $organisation = $this->getOrganisation();
        $isGoods = ($this->isGoods() === null) ? $this->isGoodsApplication() : $this->isGoods();
        if ($isGoods && $organisation->isMlh() && $organisation->getLeadTcArea() !== null) {
            return $organisation->getLeadTcArea();
        }
        return $this->getTrafficArea();
    }

    /**
     * Get licence expiry date as a date
     *
     * @return \DateTime
     * @see \Dvsa\Olcs\Api\Entity\Types\DateTimeType
     */
    public function getExpiryDateAsDate()
    {
        $expiryDate = $this->getExpiryDate();
        if ($expiryDate instanceof \DateTime || $expiryDate === null) {
            return $expiryDate;
        }
        $expiryDateAsDate = \DateTime::createFromFormat('Y-m-d', $expiryDate);
        if ($expiryDateAsDate instanceof \DateTime) {
            $expiryDateAsDate->setTime(0, 0, 0);
            $expiryDateAsDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }
        return $expiryDateAsDate;
    }

    /**
     * Get operator location
     *
     * @return string
     */
    public function getOperatorLocation()
    {
        return $this->getNiFlag() === 'N' ? 'Great Britain' : 'Northern Ireland';
    }

    /**
     * Get operator type
     *
     * @return string
     */
    public function getOperatorType()
    {
        return $this->isGoods() ? 'Goods' : 'PSV';
    }

    /**
     * Is the licence expired, ie past the expiry/continuation date
     *
     * @return bool
     */
    public function isExpired()
    {
        $expiryDate = $this->getExpiryDate(true);
        if (!$expiryDate instanceof \DateTime) {
            return false;
        }

        return $expiryDate < (new DateTime())->setTime(0, 0, 0);
    }

    /**
     * Is this licence about to expire
     * Must be withing two months of licence expiry date and have an active continuation
     *
     * @return bool
     */
    public function isExpiring()
    {
        $expiryDate = $this->getExpiryDate(true);
        // if no expiry date then false
        if (!$expiryDate instanceof \DateTime) {
            return false;
        }

        // if expired, then cannot be expiring
        if ($this->isExpired()) {
            return false;
        }

        // find the attached continuation detail entity where the licence expiry month/year matched the continaution
        $currentContinuationDetail = null;
        $continuationDetails = $this->getActiveContinuationDetails();
        /** @var ContinuationDetail $continuationDetail */
        foreach ($continuationDetails as $continuationDetail) {
            if ($continuationDetail->getContinuation()->getMonth() == $expiryDate->format('n')
                && $continuationDetail->getContinuation()->getYear() == $expiryDate->format('Y')
            ) {
                $currentContinuationDetail = $continuationDetail;
                break;
            }
        }

        // if an applicable continuation detail not found
        if ($currentContinuationDetail === null) {
            return false;
        }

        // check the licence expiry date is withing two months of now
        $now = (new DateTime())->add(new \DateInterval('P2M'));

        return $now >= $expiryDate;
    }

    /**
     * Get active continuation details
     *
     * @return ArrayCollection
     */
    public function getActiveContinuationDetails()
    {
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->in(
                    'status',
                    [
                        ContinuationDetail::STATUS_ACCEPTABLE,
                        ContinuationDetail::STATUS_UNACCEPTABLE,
                        ContinuationDetail::STATUS_PRINTED,
                    ]
                )
            );

        return $this->getContinuationDetails()->matching($criteria);
    }

    /**
     * Serialize
     *
     * @param array $bundle Bundle
     *
     * @return array
     */
    public function serialize(array $bundle = [])
    {
        $result = parent::serialize($bundle);
        // if 'isExpiring' is in bundle, then add to serialized output
        if (in_array('isExpiring', $bundle)) {
            $result['isExpiring'] = $this->isExpiring();
        }
        // if 'isExpired' is in bundle, then add to serialized output
        if (in_array('isExpired', $bundle)) {
            $result['isExpired'] = $this->isExpired();
        }

        return $result;
    }

    /**
     * Get variations with not submitted or under consideration status for this licence
     *
     * @return ArrayCollection
     */
    public function getNotSubmittedOrUnderConsiderationVariations()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isVariation', true))
            ->andWhere(
                Criteria::expr()->in(
                    'status',
                    [
                        Application::APPLICATION_STATUS_NOT_SUBMITTED,
                        Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
                    ]
                )
            );

        return $this->getApplications()->matching($criteria);
    }

    /**
     * Get O/C pending changes
     *
     * @return int
     */
    public function getOcPendingChanges()
    {
        $variations = $this->getNotSubmittedOrUnderConsiderationVariations();
        if ($variations->count() === 0) {
            return 0;
        }

        $totalChanges = 0;

        /** @var Application $variation */
        foreach ($variations as $variation) {
            $totalChanges += $variation->getOperatingCentres()->count();
            if ($variation->getTotAuthTrailers() !== $this->getTotAuthTrailers()) {
                $totalChanges++;
            }
            if ($variation->getTotAuthVehicles() !== $this->getTotAuthVehicles()) {
                $totalChanges++;
            }
        }

        return $totalChanges;
    }

    /**
     * Get O/C pending changes
     *
     * @return int
     */
    public function getTmPendingChanges()
    {
        $variations = $this->getNotSubmittedOrUnderConsiderationVariations();
        if ($variations->count() === 0) {
            return 0;
        }

        $totalChanges = 0;

        /** @var Application $variation */
        foreach ($variations as $variation) {
            $totalChanges += $variation->getTransportManagers()->count();
        }

        return $totalChanges;
    }

    /**
     * Whether the status of this licence is one required for community licence reprint
     *
     * @return bool
     */
    public function hasStatusRequiredForCommunityLicenceReprint()
    {
        $eligibleStatusIds = [
            self::LICENCE_STATUS_VALID,
            self::LICENCE_STATUS_SUSPENDED,
            self::LICENCE_STATUS_CURTAILED
        ];

        return in_array($this->getStatus()->getId(), $eligibleStatusIds);
    }
}
