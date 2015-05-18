<?php

/**
 * Licence No Gen
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Licence No Gen
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class LicenceNoGen extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Licence\LicenceNoGen';
}
