<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\TransExchange;

use Olcs\XmlTools\Xml\TemplateBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TemplateFactory
 * @package Olcs\Ebsr\Service\TransExchange
 */
class TemplateFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = '', $requestedName = '')
    {
        $array = explode('\\', $requestedName);
        $templateName = array_pop($array);

        $config = $serviceLocator->get('Config')['ebsr'];

        if (!isset($config['transexchange_publisher'])) {
            throw new \RuntimeException('Missing transexchange_publisher config');
        }

        $config = $config['transexchange_publisher'];
        if (!isset($config['templates'][$templateName])) {
            throw new \RuntimeException('Missing template ' . $templateName);
        }

        $templateService = new Template();
        $templateService->setTemplateFile($config['templates'][$templateName]);
        $templateService->setBuilder(new TemplateBuilder());

        return $templateService;
    }
}
