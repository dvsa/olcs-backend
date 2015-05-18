<?php

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Licence extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Licence\Licence';
}
