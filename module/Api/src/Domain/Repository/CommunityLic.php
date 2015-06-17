<?php

/**
 * CommunityLic
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as Entity;

/**
 * CommunityLic
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommunityLic extends AbstractRepository
{
    protected $entity = Entity::class;
}
