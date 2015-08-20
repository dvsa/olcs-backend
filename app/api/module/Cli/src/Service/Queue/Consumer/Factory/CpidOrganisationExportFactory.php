<?php

/**
 * Class CpidOrganisationExportFactory
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;

/**
 * Class CpidOrganisationExportFactory
 * @package Dvsa\Olcs\Cli\Service\Queue\Consumer
 */
class CpidOrganisationExportFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();

        $path = $sl->get('Config')['file-system']['path'];
        $repo = $sl->get('RepositoryServiceManager')->get('Organisation');
        $commandHandler = $sl->get('CommandHandlerManager');
        $fileUploader = $sl->get('FileUploader');
        $fileSystem = new Filesystem();

        return new CpidOrganisationExport(
            $path,
            $repo,
            $commandHandler,
            $fileUploader,
            $fileSystem
        );
    }
}
