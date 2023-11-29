<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Class FileProcessorFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr
 */
class FileProcessorFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FileProcessor
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileProcessor
    {
        $config = $container->get('Config');
        $tmpDir = (isset($config['tmpDirectory']) ? $config['tmpDirectory'] : sys_get_temp_dir());
        $decompressFilter = $container->get('FilterManager')->get('Decompress');
        $decompressFilter->setAdapter('zip');
        return new FileProcessor($container->get('FileUploader'), new Filesystem(), $decompressFilter, $tmpDir);
    }
}
