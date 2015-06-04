<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="permission",
 *    indexes={
 *        @ORM\Index(name="ix_permission_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_permission_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Permission extends AbstractPermission
{
    const INTERNAL_USER = 'internal-user';
    const SELFSERVE_USER = 'selfserve-user';
    const CAN_UPDATE_LICENCE_LICENCE_TYPE = 'can-update-licence-licence-type';
}
