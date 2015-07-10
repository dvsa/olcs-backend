<?php

/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractRepository extends AbstractReadonlyRepository implements RepositoryInterface
{
    public function save($entity)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only save entities of type ' . $this->entity);
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function delete($entity)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only delete entities of type ' . $this->entity);
        }

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
