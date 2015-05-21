<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConditionUndertaking Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="condition_undertaking",
 *    indexes={
 *        @ORM\Index(name="ix_condition_undertaking_added_via", columns={"added_via"}),
 *        @ORM\Index(name="ix_condition_undertaking_attached_to", columns={"attached_to"}),
 *        @ORM\Index(name="ix_condition_undertaking_condition_type", columns={"condition_type"}),
 *        @ORM\Index(name="ix_condition_undertaking_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_condition_undertaking_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_condition_undertaking_lic_condition_variation_id", columns={"lic_condition_variation_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_approval_user_id", columns={"approval_user_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_condition_undertaking_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class ConditionUndertaking extends AbstractConditionUndertaking
{

}
