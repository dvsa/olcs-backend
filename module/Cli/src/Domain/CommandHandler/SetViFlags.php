<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Set Vi Flags
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class SetViFlags extends AbstractCommandHandler
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $dbConnection;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setDbConnection($container->get('doctrine.connection.ormdefault'));
        return parent::__invoke($container, $requestedName, $options);
    }

    /**
     * Set the DB connection
     *
     * @param \Doctrine\DBAL\Connection $dbConnection DB Connection
     *
     * @return void
     */
    private function setDbConnection(\Doctrine\DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Get the DB connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    private function getDbConnection()
    {
        return $this->dbConnection;
    }

    /**
     * Handle command
     *
     * @param CommandInterface $command The command to execute
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $stmt \Doctrine\DBAL\Statement */
        $stmt = $this->getDbConnection()->prepare('CALL vi_set_flags()');
        $stmt->execute();

        $result = $stmt->fetchAll();

        if (isset($result[0]['Result'])) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\RuntimeException($result[0]['Result']);
        }
        $this->result->addMessage('VI Flags set');

        return $this->result;
    }
}
