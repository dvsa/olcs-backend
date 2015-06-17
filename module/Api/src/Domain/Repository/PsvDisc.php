<?php


namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc as Entity;

/**
 * PsvDisc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDisc extends AbstractRepository
{
    protected $entity = Entity::class;
}
