<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * SeriousInfringement Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="serious_infringement",
 *    indexes={
 *        @ORM\Index(name="ix_serious_infringement_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_serious_infringement_erru_response_user_id", columns={"erru_response_user_id"}),
 *        @ORM\Index(name="ix_serious_infringement_member_state_code", columns={"member_state_code"}),
 *        @ORM\Index(name="ix_serious_infringement_si_category_id", columns={"si_category_id"}),
 *        @ORM\Index(name="ix_serious_infringement_si_category_type_id", columns={"si_category_type_id"}),
 *        @ORM\Index(name="ix_serious_infringement_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_serious_infringement_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_serious_infringement_olbs_key_olbs_type", columns={"olbs_key","olbs_type"}),
 *        @ORM\UniqueConstraint(name="uk_serious_infringement_notification_number", columns={"notification_number"}),
 *        @ORM\UniqueConstraint(name="uk_serious_infringement_workflow_id", columns={"workflow_id"})
 *    }
 * )
 */
class SeriousInfringement extends AbstractSeriousInfringement
{
    /**
     * Updates the serious infringement with an erru response
     *
     * @param UserEntity $user
     * @param \DateTime $responseDateTime
     */
    public function updateErruResponse(UserEntity $user, \DateTime $responseDateTime)
    {
        $this->setErruResponseUser($user);
        $this->setErruResponseTime($responseDateTime);
        $this->setErruResponseSent('Y');
    }
}
