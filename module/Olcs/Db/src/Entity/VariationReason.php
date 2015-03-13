<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * VariationReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="variation_reason")
 */
class VariationReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Description45Field,
        Traits\IdIdentity;
}
