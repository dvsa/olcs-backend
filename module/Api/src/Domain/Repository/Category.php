<?php

/**
 * Category
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\Category as Entity;

/**
 * Category
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Category extends AbstractRepository
{
    protected $entity = Entity::class;
}
