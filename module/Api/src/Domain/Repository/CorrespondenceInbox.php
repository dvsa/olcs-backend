<?php

/**
 * Correspondence Inbox
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox as Entity;

/**
 * Correspondence Inbox
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CorrespondenceInbox extends AbstractRepository
{
    protected $entity = Entity::class;
}
