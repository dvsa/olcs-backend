<?php

/**
 * Transport Manager Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManager as Entity;

/**
 * Transport Manager Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManager extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'tm';
}
