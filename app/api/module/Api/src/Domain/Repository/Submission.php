<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Submission\Submission as Entity;

/**
 * Submission
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class Submission extends AbstractRepository
{
    protected $entity = Entity::class;
}
