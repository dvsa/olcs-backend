<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FileProcessorFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr
 */
class FileProcessorFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FileProcessor
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $tmpDir = (isset($config['tmpDirectory']) ? $config['tmpDirectory'] : sys_get_temp_dir());

        $decompressFilter = $container->get('FilterManager')->get('Decompress');
        $decompressFilter->setAdapter('zip');

        return new FileProcessor($container->get('FileUploader'), new Filesystem(), $decompressFilter, $tmpDir);
    }
}
