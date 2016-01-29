<?php

namespace Dvsa\Olcs\Api;

use Olcs\Logging\Log\Logger;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\ServiceManager\ServiceManager;

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

        $eventManager->getSharedManager()->attach(
            'Zend\Mvc\SendResponseListener',
            SendResponseEvent::EVENT_SEND_RESPONSE,
            function(SendResponseEvent $e) {
                $response = $e->getResponse();

                $content = $response->getContent();
                if (strlen($content) > 1000) {
                    $content = substr($content, 0, 1000) . '...';
                }

                Logger::debug('API Response Sent', ['data' => ['response' => $content]]);
            }
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
