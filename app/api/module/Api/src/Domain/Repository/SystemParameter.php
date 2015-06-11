<?php

/**
 * System Parameter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\System\SystemParameter as Entity;

/**
 * System Parameter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SystemParameter extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchValue($key)
    {
        try {
            return $this->fetchById($key);
        } catch (NotFoundException $ex) {
            return null;
        }
    }
}
