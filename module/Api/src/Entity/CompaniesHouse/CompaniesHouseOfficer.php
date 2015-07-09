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
    public function __construct(array $data)
    {
        $fields = [
            'name',
            'role',
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $method = 'set'.ucfirst($field);
                $this->$method($data[$field]);
            }
        }

        if (isset($data['dateOfBirth'])) {
            $dob = new \DateTime();
            $dob->setDate(
                $data['dateOfBirth']['year'],
                $data['dateOfBirth']['month'],
                // day element of D.o.B. is usually suppressed
                isset($data['dateOfBirth']['day']) ? $data['dateOfBirth']['day'] : 1
            );
            $this->setDateOfBirth($dob);
        }
    }
}
