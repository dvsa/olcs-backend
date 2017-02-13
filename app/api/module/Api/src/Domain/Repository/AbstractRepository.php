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
    /**
     * Save an entity
     *
     * @param mixed $entity Entity to save
     *
     * @return void
     * @throws Exception\RuntimeException
     * @throws \Exception
     */
    public function save($entity)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only save entities of type ' . $this->entity);
        }

        try {
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            \Olcs\Logging\Log\Logger::crit($e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an entity
     *
     * @param mixed $entity Entity to delete
     *
     * @return void
     * @throws Exception\RuntimeException
     * @throws \Exception
     */
    public function delete($entity)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only delete entities of type ' . $this->entity);
        }

        try {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            \Olcs\Logging\Log\Logger::crit($e->getMessage());
            throw $e;
        }
    }
}
