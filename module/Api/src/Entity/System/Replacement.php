<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * Replacement Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="replacement",
 *    indexes={
 *        @ORM\Index(name="fk_replacement_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_replacement_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Replacement extends AbstractReplacement
{

}
