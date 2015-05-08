<?php

namespace Olcs\Db;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Module class
 */
class Module implements BootstrapListenerInterface
{
    public function onBootstrap(EventInterface $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        /** @var MvcEvent $e */
        $eventManager = $e->getApplication()->getEventManager();

        // This needs to be priority 1
        $payloadValidationListener = $sm->get('PayloadValidationListener');
        $payloadValidationListener->attach($eventManager, 1);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager, 2);

        // Enable and configure Doctrine filters
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');
        $entityManager->getFilters()->enable('soft-deleteable');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
