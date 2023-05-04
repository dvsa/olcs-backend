<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention;

use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use PDO;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, Postcheck::class);
    }

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
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('DoctrineOrmEntityManager');
        $this->connection = $entityManager->getConnection()->getWrappedConnection();
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
