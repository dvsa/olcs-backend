<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * MonthlyEcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="monthly_ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_euro_emission_standard",
     *     columns={"euro_emission_standard"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_source", columns={"source"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class MonthlyEcmtPermitApplication extends AbstractMonthlyEcmtPermitApplication
{

}
