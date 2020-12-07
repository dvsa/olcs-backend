<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $config = $serviceLocator->get('Config');
        $tmpDir = (isset($config['tmpDirectory']) ? $config['tmpDirectory'] : sys_get_temp_dir());

        $decompressFilter = $serviceLocator->get('FilterManager')->get('Decompress');
        $decompressFilter->setAdapter('zip');

        return new FileProcessor($serviceLocator->get('FileUploader'), new Filesystem(), $decompressFilter, $tmpDir);
    }
}
