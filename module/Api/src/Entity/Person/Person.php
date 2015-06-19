<?php

namespace Dvsa\Olcs\Api\Entity\Person;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Person Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="person",
 *    indexes={
 *        @ORM\Index(name="ix_person_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_person_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_person_family_name", columns={"family_name"}),
 *        @ORM\Index(name="ix_person_forename", columns={"forename"}),
 *        @ORM\Index(name="ix_person_title", columns={"title"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_person_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Person extends AbstractPerson
{
    /**
     * Update person details
     *
     * @param RefData $title
     * @param string|null $forename
     * @param string|null $familyName
     * @param string|null $birthDate
     * @param string|null $birthPlace
     * @param string|null $otherName
     * @return $this
     */
    public function updatePerson(
        RefData $title = null,
        $forename = null,
        $familyName = null,
        $birthDate = null,
        $birthPlace = null,
        $otherName = null
    ) {
        $this->setTitle($title);
        $this->setForename($forename);
        $this->setFamilyName($familyName);
        $this->setBirthDate($birthDate);
        $this->setBirthPlace($birthPlace);
        $this->setOtherName($otherName);
    }
}
