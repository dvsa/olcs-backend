<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TmCaseDecision Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_case_decision",
 *    indexes={
 *        @ORM\Index(name="ix_tm_case_decision_decision", columns={"decision"}),
 *        @ORM\Index(name="ix_tm_case_decision_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_tm_case_decision_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_tm_case_decision_case_id", columns={"case_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_tm_case_decision_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TmCaseDecision extends AbstractTmCaseDecision
{

}
