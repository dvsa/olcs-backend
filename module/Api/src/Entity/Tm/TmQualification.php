<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * TmQualification Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_qualification",
 *    indexes={
 *        @ORM\Index(name="ix_tm_qualification_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_tm_qualification_country_code", columns={"country_code"}),
 *        @ORM\Index(name="ix_tm_qualification_qualification_type", columns={"qualification_type"}),
 *        @ORM\Index(name="ix_tm_qualification_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_tm_qualification_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_tm_qualification_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TmQualification extends AbstractTmQualification
{
    const ERROR_ISSUED_DATE_IN_FUTURE = 'TQ-ID-1';

    public function updateTmQualification(
        $qualificationType,
        $serialNo,
        $issuedDate,
        $countryCode,
        $tm = null,
        $createdBy = null,
        $lastModifiedBy = null
    ) {
        $this->validateTmQualification($issuedDate);

        $this->setQualificationType($qualificationType);
        $this->setSerialNo($serialNo);
        $this->setIssuedDate(new \DateTime($issuedDate));
        $this->setCountryCode($countryCode);
        if ($tm !== null) {
            $this->setTransportManager($tm);
        }
        if ($createdBy !== null) {
            $this->setCreatedBy($createdBy);
        }
        if ($lastModifiedBy !== null) {
            $this->setLastModifiedBy($lastModifiedBy);
        }
    }

    protected function validateTmQualification($issuedDate)
    {
        if (new \DateTime($issuedDate) > new DateTime('now')) {
            throw new ValidationException(
                [
                    'issuedDate' => [
                        self::ERROR_ISSUED_DATE_IN_FUTURE => 'Issued date should not be in future'
                    ]
                ]
            );
        }
    }
}
