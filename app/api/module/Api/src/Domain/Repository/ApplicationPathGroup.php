<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup as Entity;

/**
 * Application Path Group
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class ApplicationPathGroup extends AbstractRepository
{
    protected $entity = Entity::class;
}
