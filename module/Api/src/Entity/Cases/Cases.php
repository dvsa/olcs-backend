<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cases Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="cases",
 *    indexes={
 *        @ORM\Index(name="ix_cases_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_cases_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_cases_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_cases_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_cases_case_type", columns={"case_type"}),
 *        @ORM\Index(name="ix_cases_erru_case_type", columns={"erru_case_type"}),
 *        @ORM\Index(name="ix_cases_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_cases_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Cases extends AbstractCases
{

    public function getLegacyOffence($offence)
    {
        if (!isset($this->legacyOffences[$offence])) {
            throw new \InvalidArgumentException("Case legacyOffence: $offence was not found");
        }

        return $this->legacyOffences[$offence];
    }
}
