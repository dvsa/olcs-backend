<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention;

use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use PDO;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Data Retention post-delete checker
 */
class Postcheck extends AbstractQueryHandler
{
    /** @var PDOConnection */
    private $connection;

    /**
     * @param ServiceLocatorInterface|QueryHandlerManager $serviceLocator
     *
     * @return AbstractQueryHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->getServiceLocator()->get('DoctrineOrmEntityManager');
        $this->connection = $entityManager->getConnection()->getWrappedConnection();
        return parent::createService($serviceLocator);
    }

    /**
     * Execute post-check stored procedure and
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stmt = $this->connection->prepare('CALL sp_dr_postcheck();');
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $results;
    }
}
