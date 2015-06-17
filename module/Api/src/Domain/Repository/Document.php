<?php

/**
 * Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;

/**
 * Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Document extends AbstractRepository
{
    protected $entity = Entity::class;
}
