<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Diagnostics;

use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Query\Diagnostics\GenerateCheckFkIntegritySql as GenerateCheckFkIntegritySqlCmd;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use PDO;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class CheckFkIntegrity extends AbstractQueryHandler
{
    /** @var PDO */
    private $pdo;

    /**
     * @param ServiceLocatorInterface|QueryHandlerManager $serviceLocator
     *
     * @return AbstractQueryHandler
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, CheckFkIntegrity::class);
    }

    public function handleQuery(QueryInterface $query)
    {
        $queries = $this->getQueryHandler()->handleQuery(GenerateCheckFkIntegritySqlCmd::create([]))['queries'];

        foreach ($queries as $query) {
            $this->pdo->exec($query);
        }

        $fetchQuery = $this->pdo->prepare("SELECT * FROM fk_integrity_violations_tmp WHERE violations > 0");
        $fetchQuery->execute();

        $violations = [];
        foreach ($fetchQuery->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $violations[$row['fk_description']] = $row['violations'];
        }

        return [
            'fk-constraint-violation-counts' => $violations
        ];
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CheckFkIntegrity
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('DoctrineOrmEntityManager');
        $this->pdo = $entityManager->getConnection()->getWrappedConnection();
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
