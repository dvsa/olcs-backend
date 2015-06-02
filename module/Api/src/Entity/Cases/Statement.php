<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statement Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="statement",
 *    indexes={
 *        @ORM\Index(name="ix_statement_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_statement_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_statement_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_statement_contact_type", columns={"contact_type"}),
 *        @ORM\Index(name="ix_statement_statement_type", columns={"statement_type"}),
 *        @ORM\Index(name="fk_statement_contact_details1_idx", columns={"requestors_contact_details_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_statement_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Statement extends AbstractStatement
{

}
