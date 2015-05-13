<?php

namespace Dvsa\Olcs\Api\Entity\Inspection;

use Doctrine\ORM\Mapping as ORM;

/**
 * InspectionRequest Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="inspection_request",
 *    indexes={
 *        @ORM\Index(name="ix_inspection_request_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_inspection_request_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_inspection_request_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_inspection_request_task_id", columns={"task_id"}),
 *        @ORM\Index(name="ix_inspection_request_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_inspection_request_report_type", columns={"report_type"}),
 *        @ORM\Index(name="ix_inspection_request_request_type", columns={"request_type"}),
 *        @ORM\Index(name="ix_inspection_request_result_type", columns={"result_type"}),
 *        @ORM\Index(name="ix_inspection_request_requestor_user_id", columns={"requestor_user_id"}),
 *        @ORM\Index(name="ix_inspection_request_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_inspection_request_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_inspection_request_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class InspectionRequest extends AbstractInspectionRequest
{

}
