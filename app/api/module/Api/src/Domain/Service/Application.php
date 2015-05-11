<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Zend\Stdlib\ArraySerializableInterface;
use Dvsa\Olcs\Transfer\Query\Application as ApplicationQueries;
use Dvsa\Olcs\Transfer\Command\Application as ApplicationCommands;
use Doctrine\ORM\Query;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Application extends AbstractService
{
    /**
     * Handles all query objects passed to the service
     *
     * @param ArraySerializableInterface $query
     */
    public function handleQuery(ArraySerializableInterface $query)
    {
        switch (true) {
            case ($query instanceof ApplicationQueries\Application):
                return $this->getRepo()->fetchUsingId($query);
        }
    }

    /**
     * Handles all commands objects passed to the service
     *
     * @param ArraySerializableInterface $command
     */
    public function handleCommand(ArraySerializableInterface $command)
    {
        switch (true) {
            case ($command instanceof ApplicationCommands\UpdateTypeOfLicence):
                return $this->updateTypeOfLicence($command);
        }
    }

    protected function updateTypeOfLicence($command)
    {
        // Check that we have an application
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        // 404
        if ($application === null) {
            return null;
        }

        print_r(get_class($application));
        exit;
    }
}
