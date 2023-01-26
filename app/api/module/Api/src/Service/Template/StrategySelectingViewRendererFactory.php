<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class StrategySelectingViewRendererFactory
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StrategySelectingViewRendererFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StrategySelectingViewRenderer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StrategySelectingViewRenderer
    {
        return $this->__invoke($serviceLocator, StrategySelectingViewRenderer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StrategySelectingViewRenderer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StrategySelectingViewRenderer
    {
        return new StrategySelectingViewRenderer(
            $container->get('ViewRenderer'),
            $container->get('TemplateTwigRenderer'),
            $container->get('TemplateDatabaseTwigLoader')
        );
    }
}
