<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partial Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="partial",
 *    indexes={
 *        @ORM\Index(name="fk_partial_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_partial_users_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="partial_key", columns={"partial_key","prefix"})
 *    }
 * )
 */
class Partial extends AbstractPartial
{

}
