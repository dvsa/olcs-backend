<?php

/**
 * Class CpidOrganisationExportFactory
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\LockHandler;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

        $lock = new LockHandler(hash('sha256', $path));
        $lock->lock(true);

        return new CpidOrganisationExport(
            $path,
            $repo,
            $commandHandler,
            $fileUploader,
            $fileSystem,
            $lock
        );
    }
}
