<?php

/**
 * Abstract Belongs to Case Entity
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;

/**
 * Abstract Belongs to Case Entity
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
abstract class AbstractBelongsToCaseEntity extends AbstractValidator implements RepositoryManagerAwareInterface
{
    use RepositoryManagerAwareTrait;

    protected $repo;

    public function isValid($entityId, $caseId)
    {
        $entity = $this->getRepo($this->repo)->fetchById($entityId);

        return $this->belongsToCase($entity, $caseId);
    }
}
