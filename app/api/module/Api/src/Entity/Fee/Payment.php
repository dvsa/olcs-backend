<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="payment",
 *    indexes={
 *        @ORM\Index(name="ix_payment_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_payment_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_payment_payment_status", columns={"status"})
 *    }
 * )
 */
class Payment extends AbstractPayment
{

}
