<?php

/**
 * Entity Manager Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\EntityManager;

use Doctrine\ORM\EntityManager;

/**
 * Entity Manager Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait EntityManagerAwareTrait
{
    protected $em;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }
}
