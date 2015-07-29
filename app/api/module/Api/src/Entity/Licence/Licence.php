<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;

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
class Licence extends AbstractLicence
{
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
    const LICENCE_STATUS_SURRENDERED = 'lsts_surrendered';
    const LICENCE_STATUS_WITHDRAWN = 'lsts_withdrawn';
    const LICENCE_STATUS_REFUSED = 'lsts_refused';
    const LICENCE_STATUS_REVOKED = 'lsts_revoked';
    const LICENCE_STATUS_NOT_TAKEN_UP = 'lsts_ntu';
    const LICENCE_STATUS_TERMINATED = 'lsts_terminated';
    const LICENCE_STATUS_CONTINUATION_NOT_SOUGHT = 'lsts_cns';

    const TACH_EXT = 'tach_external';
    const TACH_INT = 'tach_internal';
    const TACH_NA = 'tach_na';

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
     * Gets the latest Bus Reg variation number, based on the supplied regNo
     *
     * @param string $regNo
     * @param array $notInStatus
     * @return mixed
     */
    public function getLatestBusVariation(
        $regNo,
        array $notInStatus = [
            BusReg::STATUS_REFUSED,
            BusReg::STATUS_WITHDRAWN
        ]
    ) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('regNo', $regNo))
            ->orderBy(array('variationNo' => Criteria::DESC))
            ->setMaxResults(1);

        if (!empty($notInStatus)) {
            $criteria->andWhere(Criteria::expr()->notIn('status', $notInStatus));
        }

        return $this->getBusRegs()->matching($criteria)->current();
    }

    /**
     * Gets the latest Bus Reg route number for the licence
     *
     * @return mixed
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

    public function updateTotalCommunityLicences($totalCount)
    {
        $this->totCommunityLicences = $totalCount;
    }

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

    public function getActiveCommunityLicences($licence)
    {
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->in(
                    'status',
                    [
                        CommunityLic::STATUS_PENDING,
                        CommunityLic::STATUS_ACTIVE,
                        CommunityLic::STATUS_SUSPENDED
                    ]
                )
            )->andWhere(Criteria::expr()->eq('licence', $licence));

        return $this->getCommunityLics()->matching($criteria)->current();
    }

    public function getActiveBusRoutes($licence)
    {
        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->eq('licence', $licence)
            )
            ->andWhere(
                Criteria::expr()->notIn(
                    'status',
                    [
                        BusReg::STATUS_REFUSED,
                        BusReg::STATUS_WITHDRAWN
                    ]
                )
            );

        return $this->getBusRegs()->matching($criteria)->current();
    }

    public function getActiveVariations($licence)
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
            )
            ->andWhere(Criteria::expr()->eq('licence', $licence));

        return $this->getApplications()->matching($criteria)->current();
    }

    public function getCalculatedValues()
    {
        $decisionCriteria['activeComLics'] = $this->getActiveCommunityLicences($this) !== false;
        $decisionCriteria['activeBusRoutes'] = $this->getActiveBusRoutes($this) !== false;
        $decisionCriteria['activeVariations'] = $this->getActiveVariations($this) !== false;

        $suitableForDecisions = true;

        if (in_array(true, $decisionCriteria)) {
            $suitableForDecisions = $decisionCriteria;
        }

        return [
            'suitableForDecisions' => $suitableForDecisions
        ];
    }

    public function getSerialNoPrefixFromTrafficArea()
    {
        $trafficArea = $this->getTrafficArea();

        if ($trafficArea && $trafficArea->getId() === TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
            return CommunityLicEntity::PREFIX_NI;
        }

        return CommunityLicEntity::PREFIX_GB;
    }

    public function getRemainingSpaces()
    {
        return $this->getTotAuthVehicles() - $this->getActiveVehiclesCount();
    }

    public function getActiveVehiclesCount()
    {
        return $this->getActiveVehicles()->count();
    }

    public function getActiveVehicles()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->isNull('removalDate')
        );
        $criteria->andWhere(
            $criteria->expr()->neq('specifiedDate', null)
        );

        return $this->getLicenceVehicles()->matching($criteria);
    }

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

    public function getOtherActiveLicences()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->in(
                'status',
                [
                    self::LICENCE_STATUS_SUSPENDED,
                    self::LICENCE_STATUS_VALID,
                    self::LICENCE_STATUS_CURTAILED
                ]
            )
        );
        $criteria->andWhere(
            $criteria->expr()->eq('goodsOrPsv', $this->getGoodsOrPsv())
        );
        $criteria->andWhere(
            $criteria->expr()->neq('id', $this->getId())
        );

        if ($this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV) {
            $criteria->andWhere(
                $criteria->expr()->neq('licenceType', self::LICENCE_TYPE_SPECIAL_RESTRICTED)
            );
        }

        return $this->getOrganisation()->getLicences()->matching($criteria);
    }

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

    public function isGoods()
    {
        return $this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_GOODS_VEHICLE;
    }

    public function isPsv()
    {
        return $this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV;
    }

    public function isSpecialRestricted()
    {
        return $this->getLicenceType()->getId() === self::LICENCE_TYPE_SPECIAL_RESTRICTED;
    }

    public function isRestricted()
    {
        return $this->getLicenceType()->getId() === self::LICENCE_TYPE_RESTRICTED;
    }

    public function isStandardInternational()
    {
        return $this->getLicenceType()->getId() === self::LICENCE_TYPE_STANDARD_INTERNATIONAL;
    }

    public function isStandardNational()
    {
        return $this->getLicenceType()->getId() === self::LICENCE_TYPE_STANDARD_NATIONAL;
    }

    /**
     * Helper method to get the first trading name from a licence
     * (Sorts trading names by createdOn date then alphabetically)
     *
     * @return string
     */
    public function getTradingName()
    {
        $tradingNames = (array) $this->getOrganisation()->getTradingNames()->getIterator();

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

    public function getOpenComplaintsCount()
    {
        $count = 0;
        foreach ($this->getCases() as $case) {
            foreach ($case->getComplaints() as $complaint) {
                if ($complaint->getIsCompliance() == 0 && $complaint->isOpen()) {
                    $count++;
                }
            }
        }
        return $count;
    }

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

    public function canHaveCommunityLicences()
    {
        if ($this->getLicenceType()->getId() === self::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            return true;
        }

        if ($this->isPsv() && $this->getLicenceType()->getId() === self::LICENCE_TYPE_RESTRICTED) {
            return true;
        }

        return false;
    }

    public function copyInformationFromApplication(Application $application)
    {
        $this->setLicenceType($application->getLicenceType());
        $this->setGoodsOrPsv($application->getGoodsOrPsv());
        $this->setTotAuthTrailers($application->getTotAuthTrailers());
        $this->setTotAuthVehicles($application->getTotAuthVehicles());
        $this->setTotAuthSmallVehicles($application->getTotAuthSmallVehicles());
        $this->setTotAuthMediumVehicles($application->getTotAuthMediumVehicles());
        $this->setTotAuthLargeVehicles($application->getTotAuthLargeVehicles());
        $this->setNiFlag($application->getNiFlag());
    }

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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPsvDiscsNotCeased()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('ceasedDate'));

        return $this->getPsvDiscs()->matching($criteria);
    }
}
