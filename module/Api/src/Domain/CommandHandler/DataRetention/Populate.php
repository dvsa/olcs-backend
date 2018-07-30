<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Interop\Container\ContainerInterface;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class Populate
 */
final class Populate extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /** @var Connection */
    private $connection;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('DoctrineOrmEntityManager');
        $this->connection = $entityManager->getConnection();
        return parent::__invoke($container, $requestedName, $options);
    }

    protected $repoServiceName = 'DataRetentionRule';

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var DataRetentionRule $repo */
        $repo = $this->getRepo();

        /** @var \Dvsa\Olcs\Api\Entity\DataRetentionRule $dataRetentionRule */
        $enabledRules = $repo->fetchEnabledRules();

        foreach ($enabledRules['results'] as $dataRetentionRule) {
            $this->result->addMessage(
                sprintf(
                    'Running rule id %s, %s',
                    $dataRetentionRule->getId(),
                    $dataRetentionRule->getPopulateProcedure()
                )
            );
            try {
                $this->connection->beginTransaction();
                $result = $repo->runProc(
                    $dataRetentionRule->getPopulateProcedure(),
                    $this->getCurrentUser()->getId()
                );
                $this->connection->commit();
            } catch (\Exception $e) {
                $this->result->addMessage($e->getMessage());
                Logger::err(
                    sprintf(
                        'Error on rule id %s, %s: %s',
                        $dataRetentionRule->getId(),
                        $dataRetentionRule->getPopulateProcedure(),
                        $e->getMessage()
                    )
                );
                $this->connection->rollBack();
            }

            if (!$result) {
                $this->result->addMessage(
                    sprintf(
                        'Rule id %s, %s returned FALSE',
                        $dataRetentionRule->getId(),
                        $dataRetentionRule->getPopulateProcedure()
                    )
                );
            }
        }

        return $this->result;
    }
}
