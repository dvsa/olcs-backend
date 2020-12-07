<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Diagnostics;

use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Query\Diagnostics\GenerateCheckFkIntegritySql as GenerateCheckFkIntegritySqlCmd;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use PDO;
use Laminas\ServiceManager\ServiceLocatorInterface;

final class CheckFkIntegrity extends AbstractQueryHandler
{
    /** @var PDO */
    private $pdo;

    /**
     * @param ServiceLocatorInterface|QueryHandlerManager $serviceLocator
     *
     * @return AbstractQueryHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->getServiceLocator()->get('DoctrineOrmEntityManager');
        $this->pdo = $entityManager->getConnection()->getWrappedConnection();
        return parent::createService($serviceLocator);
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
}
