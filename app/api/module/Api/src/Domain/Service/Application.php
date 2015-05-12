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
    protected $queryMap = [
        ApplicationQueries\Application::class => 'fetchUsingId'
    ];

    protected $commandMap = [
        ApplicationCommands\UpdateTypeOfLicence::class => 'updateTypeOfLicence'
    ];

    protected function fetchUsingId($query)
    {
        return $this->getRepo()->fetchUsingId($query);
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
