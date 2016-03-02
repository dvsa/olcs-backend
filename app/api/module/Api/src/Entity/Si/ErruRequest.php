<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErruRequest Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="erru_request",
 *    indexes={
 *        @ORM\Index(name="ix_erru_request_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_erru_request_response_user_id", columns={"response_user_id"}),
 *        @ORM\Index(name="ix_erru_request_member_state_code", columns={"member_state_code"}),
 *        @ORM\Index(name="ix_erru_request_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_erru_request_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_erru_request_case_type", columns={"case_type"}),
 *        @ORM\Index(name="ix_erru_request_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_erru_request_workflow_id", columns={"workflow_id"}),
 *        @ORM\UniqueConstraint(name="uk_erru_request_case_id", columns={"case_id"})
 *    }
 * )
 */
class ErruRequest extends AbstractErruRequest
{

}
