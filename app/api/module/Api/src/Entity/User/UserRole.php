<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserRole Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_role",
 *    indexes={
 *        @ORM\Index(name="ix_user_role_role_id", columns={"role_id"}),
 *        @ORM\Index(name="ix_user_role_user_id", columns={"user_id"}),
 *        @ORM\Index(name="ix_user_role_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_user_role_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class UserRole extends AbstractUserRole
{

}
