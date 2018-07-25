<?php

namespace Dvsa\Olcs\Email\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $templateRenderer = new TemplateRenderer();
        $templateRenderer->setViewRenderer($container->get('ViewRenderer'));

        return $templateRenderer;
    }
}
