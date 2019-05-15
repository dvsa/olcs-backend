<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TwigRenderer(
            $serviceLocator->get('TemplateTwigEnvironment')
        );
    }
}
