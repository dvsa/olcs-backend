<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompaniesHouseAlert Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="companies_house_alert",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_alert_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_companies_house_alert_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_companies_house_alert_organisation_id", columns={"organisation_id"})
 *    }
 * )
 */
class CompaniesHouseAlert extends AbstractCompaniesHouseAlert
{

}
