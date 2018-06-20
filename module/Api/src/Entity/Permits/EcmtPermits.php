<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
/**
 * EcmtPermits Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permits",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permits_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permits_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permits_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permits_payment_status", columns={"payment_status"}),
 *        @ORM\Index(name="ix_ecmt_permits_ecmt_permits_application_id",
     *     columns={"ecmt_permits_application_id"})
 *    }
 * )
 */
class EcmtPermits extends AbstractEcmtPermits
{
    /**
     * Create new EcmtPermits
     *
     * @param RefData               $status          Status
     * @param RefData               $paymentStatus    Payment status
     * @param EcmtPermitApplication $ecmtPermitApplication    Permit application
     * @param string                $intensity
     * @param array                 $countries
     *
     * @return BusReg
     */
    public static function createNew(
      RefData $status,
      RefData $paymentStatus,
      EcmtPermitApplication $ecmtPermitApplication,
      $intensity,
      $countries

    ) {
        $ecmtPermits = new self();
        $ecmtPermits->setStatus($status);
        $ecmtPermits->setPaymentStatus($paymentStatus);

        $ecmtPermits->setEcmtPermitsApplication($ecmtPermitApplication);
        $ecmtPermits->setIntensity($intensity);
        $ecmtPermits->addCountrys($countries);

        return $ecmtPermits;
    }
}
