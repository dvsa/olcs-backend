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
    const SYSTEM_ADMIN = 'system-admin';
    const INTERNAL_ADMIN = 'internal-admin';
    const INTERNAL_USER = 'internal-user';
    const INTERNAL_EDIT = 'internal-edit';
    const LOCAL_AUTHORITY_ADMIN = 'local-authority-admin';
    const LOCAL_AUTHORITY_USER = 'local-authority-user';
    const OPERATOR_ADMIN = 'operator-admin';
    const OPERATOR_USER = 'operator-user';
    const PARTNER_ADMIN = 'partner-admin';
    const PARTNER_USER = 'partner-user';
    const SELFSERVE_USER = 'selfserve-user';
    const CAN_UPDATE_LICENCE_LICENCE_TYPE = 'can-update-licence-licence-type';
    const CAN_MANAGE_USER_INTERNAL = 'can-manage-user-internal';
    const CAN_MANAGE_USER_SELFSERVE = 'can-manage-user-selfserve';
    const SELFSERVE_EBSR_LIST = 'selfserve-ebsr-list';
    const SELFSERVE_EBSR_UPLOAD = 'selfserve-ebsr-upload';
    const SELFSERVE_EBSR_DOCUMENTS = 'selfserve-ebsr-documents';
    const CAN_READ_USER_SELFSERVE = 'can-read-user-selfserve';
    const TRANSPORT_MANAGER ='selfserve-tm';
}
