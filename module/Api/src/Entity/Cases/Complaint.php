<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Complaint Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="complaint",
 *    indexes={
 *        @ORM\Index(name="ix_complaint_complainant_contact_details_id", columns={"complainant_contact_details_id"}),
 *        @ORM\Index(name="ix_complaint_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_complaint_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_complaint_status", columns={"status"}),
 *        @ORM\Index(name="ix_complaint_complaint_type", columns={"complaint_type"}),
 *        @ORM\Index(name="ix_complaint_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_complaint_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Complaint extends AbstractComplaint
{

}
