<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\EcmtSiftingSettings as Entity;

class SiftingSettings extends AbstractRepository
{
    protected $entity = Entity::class;
}
