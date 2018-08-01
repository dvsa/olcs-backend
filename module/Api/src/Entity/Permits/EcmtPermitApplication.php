<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * EcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_permit_type", columns={"permit_type"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_payment_status", columns={"payment_status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_sectors_id", columns={"sectors_id"})
 *    }
 * )
 */
class EcmtPermitApplication extends AbstractEcmtPermitApplication
{
    const STATUS_CANCELLED = 'ecmt_permit_cancelled';
    const STATUS_NOT_YET_SUBMITTED = 'ecmt_permit_nys';
    const STATUS_UNDER_CONSIDERATION = 'ecmt_permit_uc';
    const STATUS_WITHDRAWN = 'ecmt_permit_withdrawn';
    const STATUS_AWAITING_FEE = 'ecmt_permit_awaiting';
    const STATUS_UNSUCCESSFUL = 'ecmt_permit_unsuccessful';
    const STATUS_ISSUED = 'ecmt_permit_issued';

    /**
     * Create new EcmtPermitApplication
     *
     * @param RefData               $status           Status
     * @param RefData               $paymentStatus    Payment status
     * @param RefData               $permitType       Permit type
     * @param Licence               $licence          Licence
     *
     * @return BusReg
     */
    public static function createNew(
      RefData $status,
      RefData $paymentStatus,
      RefData $permitType,
      Licence $licence
    ) {
        $ecmtPermitApplication = new self();
        $ecmtPermitApplication->setStatus($status);
        $ecmtPermitApplication->setPaymentStatus($paymentStatus);
        $ecmtPermitApplication->setPermitType($permitType);
        $ecmtPermitApplication->setLicence($licence);

        return $ecmtPermitApplication;
    }
}
