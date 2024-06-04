<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Laminas\Log\PsrLoggerAdapter;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Log\LaminasLogPsr3Adapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class EbsrProcessingChainFactory implements FactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EbsrProcessingChain
    {
        return $this->__invoke($serviceLocator, EbsrProcessingChain::class);
    }


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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EbsrProcessingChain
    {
        $logger = new LaminasLogPsr3Adapter($container->get('Logger'));
        $toggleService = $container->get(ToggleService::class);
        if( $toggleService->isEnabled(FeatureToggle::BACKEND_TRANSXCHANGE)) {
            $logger->info('TXC toggle on');
            return new EbsrProcessingChain($logger, $container->get(ZipProcessor::class), $container->get(S3Processor::class));
        }
        return new EbsrProcessingChain($logger, $container->get(FileProcessor::class));
    }
}