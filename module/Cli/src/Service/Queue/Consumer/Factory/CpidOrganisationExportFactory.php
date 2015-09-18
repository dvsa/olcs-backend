<?php

/**
 * Cpid Organisation Export Factory
 *
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;

/**
 * Cpid Organisation Export Factory
 *
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CpidOrganisationExportFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();

        $repo = $sl->get('RepositoryServiceManager')->get('Organisation');
        $commandHandler = $sl->get('CommandHandlerManager');

        return new CpidOrganisationExport($repo, $commandHandler);
    }
}
