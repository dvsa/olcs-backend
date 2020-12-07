<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StrategySelectingViewRenderer(
            $serviceLocator->get('ViewRenderer'),
            $serviceLocator->get('TemplateTwigRenderer'),
            $serviceLocator->get('TemplateDatabaseTwigLoader')
        );
    }
}
