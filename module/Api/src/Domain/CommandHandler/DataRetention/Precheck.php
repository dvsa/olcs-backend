<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * DR Precheck
 */
final class Precheck extends AbstractCommandHandler
{
    /** @var Connection */
    private $connection;

    /**
     * @param ServiceLocatorInterface|QueryHandlerManager $serviceLocator
     *
     * @return AbstractCommandHandler|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->getServiceLocator()->get('DoctrineOrmEntityManager');
        $this->connection = $entityManager->getConnection()->getWrappedConnection();
        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $limit = (int)$command->getLimit();
        $stmt = $this->connection->prepare("CALL sp_dr_precheck($limit);");
        $stmt->execute();
        $this->result->addMessage("Precheck procedure executed.");
        return $this->result;
    }
}
