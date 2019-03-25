<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeeType Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="fee_type",
 *    indexes={
 *        @ORM\Index(name="ix_fee_type_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_fee_type_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_fee_type_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_fee_type_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_type_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_fee_type_accrual_rule", columns={"accrual_rule"}),
 *        @ORM\Index(name="ix_fee_type_fee_type", columns={"fee_type"}),
 *        @ORM\Index(name="ix_fee_type_is_miscellaneous", columns={"is_miscellaneous"})
 *    }
 * )
 */
class FeeType extends AbstractFeeType
{
    const FEE_TYPE_APP = 'APP';
    const FEE_TYPE_VAR = 'VAR';
    const FEE_TYPE_GRANT = 'GRANT';
    const FEE_TYPE_CONT = 'CONT';
    const FEE_TYPE_VEH = 'VEH';
    const FEE_TYPE_GRANTINT = 'GRANTINT';
    const FEE_TYPE_INTVEH = 'INTVEH';
    const FEE_TYPE_DUP = 'DUP';
    const FEE_TYPE_ANN = 'ANN';
    const FEE_TYPE_GRANTVAR = 'GRANTVAR';
    const FEE_TYPE_BUSAPP = 'BUSAPP';
    const FEE_TYPE_BUSVAR = 'BUSVAR';
    const FEE_TYPE_GVANNVEH = 'GVANNVEH';
    const FEE_TYPE_INTUPGRADEVEH = 'INTUPGRADEVEH';
    const FEE_TYPE_INTAMENDED = 'INTAMENDED';
    const FEE_TYPE_IRFOPSVAPP = 'IRFOPSVAPP';
    const FEE_TYPE_IRFOPSVANN = 'IRFOPSVANN';
    const FEE_TYPE_IRFOPSVCOPY = 'IRFOPSVCOPY';
    const FEE_TYPE_IRFOGVPERMIT = 'IRFOGVPERMIT';
    const FEE_TYPE_ADJUSTMENT = 'ADJUSTMENT';

    const FEE_TYPE_IRHP_APP = 'IRHPGVAPP';
    const FEE_TYPE_IRHP_ISSUE = 'IRHPGVISSUE';

    const FEE_TYPE_ECMT_APP = 'IRHPGVAPP';
    const FEE_TYPE_ECMT_ISSUE = 'IRHPGVISSUE';

    const FEE_TYPE_ECMT_APP_PRODUCT_REF = 'IRHP_GV_APP_ECMT';
    const FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF = 'IRHP_GV_APP_BILATERAL_ANN';
    const FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF = 'IRHP_GV_PERMIT_BILATERAL_ANN';

    const COST_CENTRE_REF_TYPE_LICENSING = 'TA';
    const COST_CENTRE_REF_TYPE_IRFO = 'IR';

    // country codes for CPMS
    const COUNTRY_CODE_GB = 'GB';
    const COUNTRY_CODE_NI = 'NI';

    const FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF = 'IRHP_GV_ECMT_100_PERMIT_FEE';
    const FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF = 'IRHP_GV_ECMT_75_PERMIT_FEE';
    const FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF = 'IRHP_GV_ECMT_50_PERMIT_FEE';
    const FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF = 'IRHP_GV_ECMT_25_PERMIT_FEE';

    /**
     * Alias of getIsMiscellaneous
     * @return bool
     */
    public function isMiscellaneous()
    {
        return (bool) $this->getIsMiscellaneous();
    }

    public function isAdjustment()
    {
        return $this->getFeeType()->getId() === self::FEE_TYPE_ADJUSTMENT;
    }

    public function getCalculatedBundleValues()
    {
        return [
            'displayValue' => $this->getAmount(),
        ];
    }

    /**
     * AC from OLCS-10611
     * @return string amount
     */
    public function getAmount()
    {
        return $this->getFixedValue() > 0 ? $this->getFixedValue() : $this->getFiveYearValue();
    }

    /**
     * Get the CPMS country code based on the is_ni flag
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getIsNi() === 'Y' ? self::COUNTRY_CODE_NI : self::COUNTRY_CODE_GB;
    }

    /**
     * Is show quantity
     *
     * @return bool
     */
    public function isShowQuantity()
    {
        return in_array(
            $this->getFeeType()->getId(),
            [
                self::FEE_TYPE_IRFOPSVCOPY,
                self::FEE_TYPE_IRFOPSVANN,
                self::FEE_TYPE_IRFOGVPERMIT
            ]
        );
    }

    /**
     * @return bool
     */
    public function isEcmtApplication()
    {
        return $this->feeType->getId() === self::FEE_TYPE_ECMT_APP;
    }

    /**
     * @return bool
     */
    public function isEcmtIssue()
    {
        return $this->feeType->getId() === self::FEE_TYPE_ECMT_ISSUE;
    }

    /**
     * @return bool
     */
    public function isInterimGrantFee()
    {
        return $this->getFeeType()->getId() === self::FEE_TYPE_GRANTINT;
    }

    /**
     * @return bool
     */
    public function isIrhpApplicationIssue()
    {
        return in_array($this->feeType->getId(), [self::FEE_TYPE_IRHP_ISSUE, self::FEE_TYPE_IRHP_APP]);
    }
}
