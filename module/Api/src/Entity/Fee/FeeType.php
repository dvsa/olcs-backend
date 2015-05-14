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
}
