<?php

/**
 * GoodsDisc.php
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc as Entity;

/**
 * GoodsDisc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsDisc extends AbstractRepository
{
    protected $entity = Entity::class;
}
