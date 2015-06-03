<?php

namespace Dvsa\Olcs\Api\Entity\Bus;

use Doctrine\ORM\Mapping as ORM;

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
 *        @ORM\Index(name="fk_bus_reg_operating_centre1", columns={"operating_centre_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_bus_reg_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class BusReg extends AbstractBusReg
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

    /**
     * Returns whether the variation is the latest one
     *
     * @return bool
     */
    public function isLatestVariation()
    {
        return $this->getId() === $this->getLicence()->getLatestBusVariation($this->getRegNo())->getId();
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
            'isLatestVariation' => $this->isLatestVariation()
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
    )
    {
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
    )
    {
        $this->setIsQualityPartnership($isQualityPartnership);
        $this->setQualityPartnershipDetails($qualityPartnershipDetails);
        $this->setQualityPartnershipFacilitiesUsed($qualityPartnershipFacilitiesUsed);
        $this->setIsQualityContract($isQualityContract);
        $this->setQualityContractDetails($qualityContractDetails);

        return true;
    }
}
