<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $tmpDir = ($config['tmpDirectory'] ?? sys_get_temp_dir());
        $decompressFilter = $container->get('FilterManager')->get('Decompress');
        $decompressFilter->setAdapter('zip');

        $fileProcessor = new FileProcessor($container->get('FileUploader'), new Filesystem(), $decompressFilter, $container->get(ZipProcessor::class), $tmpDir);

        if (isset($config['ebsr']['tmp_extra_path'])) {
            $fileProcessor->setSubDirPath($config['ebsr']['tmp_extra_path']);
        }
        return  $fileProcessor;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createService(ContainerInterface $serviceLocator): FileProcessor
    {
        return $this->__invoke($serviceLocator, FileProcessor::class);
    }
}
