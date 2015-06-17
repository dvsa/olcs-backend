<?php

/**
 * Opposition
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Opposition\Opposition as Entity;

/**
 * Opposition
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Opposition extends AbstractRepository
{
    protected $entity = Entity::class;
}
