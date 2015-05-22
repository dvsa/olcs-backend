<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompaniesHouseAlertReason Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="companies_house_alert_reason",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_alert_reason_companies_house_alert_id",
     *     columns={"companies_house_alert_id"}),
 *        @ORM\Index(name="ix_companies_house_alert_reason_reason_type", columns={"reason_type"})
 *    }
 * )
 */
class CompaniesHouseAlertReason extends AbstractCompaniesHouseAlertReason
{

}
