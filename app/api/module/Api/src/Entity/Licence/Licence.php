<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< Updated upstream
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
=======
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
>>>>>>> Stashed changes
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\OlcsTest\Api\Entity\CommunityLic\CommunityLicEntityTest;

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
        return ($this->getGoodsOrPsv()->getId() === self::LICENCE_CATEGORY_PSV
            && $this->getLicenceType()->getId() === self::LICENCE_TYPE_SPECIAL_RESTRICTED
        );
    }

    /**
     * Gets the latest Bus Reg variation number, based on the supplied regNo
     *
     * @param $regNo
     * @return mixed
     */
    public function getLatestBusVariation($regNo)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('regNo', $regNo))
            ->andWhere(Criteria::expr()->notIn('status', [BusReg::STATUS_REFUSED, BusReg::STATUS_WITHDRAWN]))
            ->orderBy(array('variationNo' => Criteria::DESC))
            ->setMaxResults(1);

        return $this->getBusRegs()->matching($criteria)->current();
    }

    public function updateSafetyDetails(
        $safetyInsVehicles,
        $safetyInsTrailers,
        $tachographIns,
        $tachographInsName,
        $safetyInsVaries
    )
    {
        if ($tachographIns !== null && $tachographIns !== self::TACH_NA && empty($tachographInsName)) {
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

        if (empty($safetyInsTrailers)) {
            $safetyInsTrailers = null;
        }

        $this->setSafetyInsTrailers($safetyInsTrailers);

        $this->setTachographIns($tachographIns);

        $this->setTachographInsName($tachographInsName);

        $this->setSafetyInsVaries($safetyInsVaries);
    }

    public function getActiveCommunityLicences($licence)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()
                ->in('status', [
                        CommunityLic::STATUS_PENDING,
                        CommunityLic::STATUS_ACTIVE,
                        CommunityLic::STATUS_SUSPENDED
                    ])
            )->andWhere(Criteria::expr()->eq('licence', $licence));

        return $this->getCommunityLics()->matching($criteria)->current();
    }

    public function getActiveBusRoutes($licence)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('licence', $licence))
            ->andWhere(Criteria::expr()->notIn('status', [BusReg::STATUS_REFUSED, BusReg::STATUS_WITHDRAWN]));

        return $this->getBusRegs()->matching($criteria)->current();
    }

    public function getActiveVariations($licence)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isVariation', true))
            ->andWhere(Criteria::expr()->eq('licence', $licence));

        return $this->getApplications()->matching($criteria)->current();
    }

    public function getCalculatedValues()
    {
        $result = function () {
            $decisionCriteria['activeComLics'] = ($this->getActiveCommunityLicences($this) !== false ? true : false);
            $decisionCriteria['activeBusRoutes'] = ($this->getActiveBusRoutes($this) !== false ? true : false);
            $decisionCriteria['activeVariations'] = ($this->getActiveVariations($this) !== false ? true : false);

            if (in_array(true, $decisionCriteria)) {
                return $decisionCriteria;
            }

            return true;
        };

        return [
            'suitableForDecisions' => $result()
        ];
    }
}
