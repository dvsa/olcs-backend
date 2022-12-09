<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class TwigRendererFactory
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TwigRendererFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TwigRenderer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TwigRenderer
    {
        return $this->__invoke($serviceLocator, TwigRenderer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TwigRenderer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TwigRenderer
    {
        return new TwigRenderer(
            $container->get('TemplateTwigEnvironment')
        );
    }
}
