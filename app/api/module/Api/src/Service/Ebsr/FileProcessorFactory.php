<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileProcessorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $tmpDir = (isset($config['tmpDirectory']) ? $config['tmpDirectory'] : sys_get_temp_dir());

        $decompressFilter = $serviceLocator->get('FilterManager')->get('Decompress');
        $decompressFilter->setAdapter('zip');

        return new FileProcessor($serviceLocator->get('FileUploader'), new Filesystem(), $decompressFilter, $tmpDir);
    }
}
