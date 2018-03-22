<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentStatus Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="payment_status",
 *    indexes={
 *        @ORM\Index(name="ecmt_payment_status_created_by", columns={"created_by"})
 *    }
 * )
 */
class PaymentStatus extends AbstractPaymentStatus
{

}
