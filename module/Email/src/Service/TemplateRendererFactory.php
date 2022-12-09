<?php

namespace Dvsa\Olcs\Email\Service;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class TemplateRenderer
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TemplateRendererFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TemplateRenderer
    {
        return $this->__invoke($serviceLocator, TemplateRenderer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TemplateRenderer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TemplateRenderer
    {
        $templateRenderer = new TemplateRenderer();
        $templateRenderer->setViewRenderer($container->get('TemplateStrategySelectingViewRenderer'));
        return $templateRenderer;
    }
}
