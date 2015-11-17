<?php

/**
 * Abstract Can Reopen Entity
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;

/**
 * Abstract Can Reopen Entity
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
abstract class AbstractCanReopenEntity extends AbstractValidator implements RepositoryManagerAwareInterface
{
    use RepositoryManagerAwareTrait;

    protected $repo;

    public function isValid($entityId)
    {
        $entity = $this->getRepo($this->repo)->fetchById($entityId);

        return $this->canReopen($entity);
    }
}
