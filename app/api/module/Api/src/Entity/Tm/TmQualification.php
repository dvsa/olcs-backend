<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

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

}
