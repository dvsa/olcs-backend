<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
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

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param CreateViExtractFiles
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->setDbConnection($mainServiceLocator->get('doctrine.connection.ormdefault'));

        return parent::createService($serviceLocator);
    }

    /**
     * @param \Doctrine\DBAL\Connection $dbConnection
     */
    private function setDbConnection(\Doctrine\DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getDbConnection()
    {
        return $this->dbConnection;
    }

    /**
     * Handle command
     *
     * @param CommandInterface $command
     * @param Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $stmt \Doctrine\DBAL\Statement */
        $stmt = $this->getDbConnection()->prepare('CALL vi_set_flags()');
        $stmt->execute();

        $result = $stmt->fetchAll();

        if (isset($result[0]['Result']) && $result[0]['Result'] == 0) {
            $this->result->addMessage('VI Flags set');
        } else {
            throw new \Dvsa\Olcs\Api\Domain\Exception\RuntimeException('Error running stored procedure vi_set_flags');
        }

        return $this->result;
    }
}
