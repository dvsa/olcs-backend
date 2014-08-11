<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LicenceNoGen Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="licence_no_gen",
 *    indexes={
 *        @ORM\Index(name="fk_licence_no_gen_application1_idx", columns={"application_id"})
 *    }
 * )
 */
class LicenceNoGen implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ApplicationManyToOne;

}
