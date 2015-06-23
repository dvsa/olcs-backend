<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\SubCategory as Entity;

/**
 * SubCategory
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubCategory extends AbstractRepository
{
    protected $entity = Entity::class;
}
