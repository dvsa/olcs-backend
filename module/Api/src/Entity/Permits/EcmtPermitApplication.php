<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;


/**
 * EcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_payment_status", columns={"payment_status"})
 *    }
 * )
 */
class EcmtPermitApplication extends AbstractEcmtPermitApplication
{

    /**
     * Create new EcmtPermitApplication
     *
     * @param RefData               $status          Status
     * @param RefData               $paymentStatus    Payment status
     *
     * @return BusReg
     */
    public static function createNew(
      RefData $status,
      RefData $paymentStatus
    ) {
        $ecmtPermitApplication = new self();
        $ecmtPermitApplication->setStatus($status);
        $ecmtPermitApplication->setPaymentStatus($paymentStatus);

        return $ecmtPermitApplication;
    }
}
