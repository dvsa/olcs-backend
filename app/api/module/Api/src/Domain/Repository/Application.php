<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Application\Application as Entity;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
/*final */class Application extends AbstractRepository
{
    protected $entity = Entity::class;
}
