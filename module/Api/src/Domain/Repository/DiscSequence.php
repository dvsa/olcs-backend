<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\DiscSequence as Entity;

/**
 * Disc Sequence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DiscSequence extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'ds';
}
