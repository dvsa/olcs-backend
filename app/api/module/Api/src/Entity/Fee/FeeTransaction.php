<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeeTransaction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="fee_transaction",
 *    indexes={
 *        @ORM\Index(name="ix_fee_transaction_transaction_id", columns={"transaction_id"}),
 *        @ORM\Index(name="ix_fee_transaction_fee_id", columns={"fee_id"}),
 *        @ORM\Index(name="ix_fee_transaction_reversed_fee_transaction_id",
 *     columns={"reversed_fee_transaction_id"}),
 *        @ORM\Index(name="ix_fee_transaction_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_transaction_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_fee_transaction_fee_id_transaction_id",
 *     columns={"fee_id","transaction_id"})
 *    }
 * )
 */
class FeeTransaction extends AbstractFeeTransaction
{

}
