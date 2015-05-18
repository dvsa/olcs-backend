<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
/*final */class Application extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Application\Application';
}
