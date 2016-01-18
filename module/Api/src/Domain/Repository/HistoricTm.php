<?php

/**
 * Historic TM
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManager as Entity;

/**
 * Historic TM Repo
 */
class HistoricTm extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'historic_tm';
}
