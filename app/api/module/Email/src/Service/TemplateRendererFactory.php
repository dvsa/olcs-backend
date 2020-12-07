<?php

namespace Dvsa\Olcs\Email\Service;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $templateRenderer = new TemplateRenderer();
        $templateRenderer->setViewRenderer($serviceLocator->get('TemplateStrategySelectingViewRenderer'));

        return $templateRenderer;
    }
}
