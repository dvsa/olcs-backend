<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;
use Laminas\Log\PsrLoggerAdapter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Finder\Finder;

class ZipProcessorFactory implements FactoryInterface
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ZipProcessor
    {
        $config = $container->get('Config');
        $tmpDir = ($config['tmpDirectory'] ?? sys_get_temp_dir());
        $decompressFilter = $container->get('FilterManager')->get('Decompress');
        $decompressFilter->setAdapter('zip');
        $logger = new PsrLoggerAdapter($container->get('Logger'));
        $finder = new Finder();
        $zipProcessor = new ZipProcessor($container->get('FileUploader'), new Filesystem(), $decompressFilter, $tmpDir, $logger, $finder);

        if (isset($config['ebsr']['tmp_extra_path'])) {
            $zipProcessor->setSubDirPath($config['ebsr']['tmp_extra_path']);
        }
        return $zipProcessor;
    }

    public function createService(ServiceLocatorInterface $serviceLocator): ZipProcessor
    {
        return $this->__invoke($serviceLocator, ZipProcessor::class);
    }
}
