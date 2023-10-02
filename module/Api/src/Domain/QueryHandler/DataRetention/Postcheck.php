<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention;

use Doctrine\DBAL\Driver\PDO\Connection as PDOConnection;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Data Retention post-delete checker
 */
class Postcheck extends AbstractQueryHandler
{
    /** @var PDOConnection|ServerInfoAwareConnection  */
    private ServerInfoAwareConnection $connection;

    /**
     * Execute post-check stored procedure and
     *
     * @param QueryInterface $query
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var \PDOStatement $stmt */
        $stmt = $this->connection->prepare('CALL sp_dr_postcheck();');
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $results;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Postcheck
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;

        /** @var EntityManager $entityManager */
        $entityManager = $container->get('DoctrineOrmEntityManager');
        $this->connection = $entityManager->getConnection()->getNativeConnection();
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
