<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeePayment Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="fee_payment",
 *    indexes={
 *        @ORM\Index(name="ix_fee_payment_payment_id", columns={"payment_id"}),
 *        @ORM\Index(name="ix_fee_payment_fee_id", columns={"fee_id"}),
 *        @ORM\Index(name="ix_fee_payment_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_payment_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_fee_payment_fee_id_payment_id", columns={"fee_id","payment_id"})
 *    }
 * )
 */
class FeePayment extends AbstractFeePayment
{

}
