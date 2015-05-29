<?php

namespace Dvsa\Olcs\Api\Entity\CompaniesHouse;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompaniesHouseOfficer Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="companies_house_officer",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_officer_companies_house_company_id",
     *     columns={"companies_house_company_id"})
 *    }
 * )
 */
class CompaniesHouseOfficer extends AbstractCompaniesHouseOfficer
{

}
