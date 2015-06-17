<?php

/**
 * DocTemplate
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as Entity;

/**
 * DocTemplate
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocTemplate extends AbstractRepository
{
    protected $entity = Entity::class;
}
