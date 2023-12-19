<?php

/**
 * Class TransactionManager
 * @package Dvsa\Olcs\Api\Domain\Repository
 */

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TransactionManager
 * @package Dvsa\Olcs\Api\Domain\Repository
 */
final class TransactionManager implements TransactionManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function beginTransaction()
    {
        $this->em->beginTransaction();
    }

    public function commit()
    {
        $this->em->commit();
    }

    public function rollback()
    {
        $this->em->rollback();
    }
}
