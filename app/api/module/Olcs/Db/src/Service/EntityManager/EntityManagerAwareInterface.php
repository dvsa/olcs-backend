<?php

/**
 * Entity Manager Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\EntityManager;

use Doctrine\ORM\EntityManager;

/**
 * Entity Manager Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface EntityManagerAwareInterface
{
    public function setEntityManager(EntityManager $em);

    public function getEntityManager();
}
