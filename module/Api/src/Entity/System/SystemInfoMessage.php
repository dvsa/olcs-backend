<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemInfoMessage Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="system_info_message",
 *    indexes={
 *        @ORM\Index(name="ix_system_info_message_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_system_info_message_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SystemInfoMessage extends AbstractSystemInfoMessage
{

}
