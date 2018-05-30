<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentStatus Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="payment_status",
 *    indexes={
 *        @ORM\Index(name="payment_status_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_ecmt_payment_status_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PaymentStatus extends AbstractPaymentStatus
{

}
