<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\Category as Entity;

/**
 * Category
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Category extends AbstractRepository
{
    protected $entity = Entity::class;
}
