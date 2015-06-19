<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\SubCategoryDescription as Entity;

/**
 * SubCategoryDescription
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubCategoryDescription extends AbstractRepository
{
    protected $entity = Entity::class;
}
