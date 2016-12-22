<?php

namespace Dvsa\Olcs\Api;

use Olcs\Logging\Log\Logger;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ResponseSender\SendResponseEvent;

/**
 * Module class
 */
class Module implements BootstrapListenerInterface
{
    private static $allowEmptyStatusCodes = [
        \Zend\Http\PhpEnvironment\Response::STATUS_CODE_204,
        \Zend\Http\PhpEnvironment\Response::STATUS_CODE_206,
    ];

    /**
     * Bootstrap
     *
     * @param EventInterface $e Event
     *
     * @return void
     */
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
            function (SendResponseEvent $e) {
                $this->logResponse($e->getResponse());
            }
        );

        $this->setLoggerUser($e->getApplication()->getServiceManager());
    }

    /**
     * Config
     *
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Set the user ID in the log processor so that it can be included in the log files
     *
     * @param \Zend\ServiceManager\ServiceManager $serviceManager Service Manager
     *
     * @return void
     */
    private function setLoggerUser(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $authService = $serviceManager->get(\ZfcRbac\Service\AuthorizationService::class);
        $serviceManager->get('LogProcessorManager')->get(\Olcs\Logging\Log\Processor\UserId::class)
            ->setUserId($authService->getIdentity()->getUser()->getLoginId());
    }

    /**
     * Add details of the response to the log
     *
     * @param \Zend\Stdlib\ResponseInterface $response Response
     *
     * @return void
     */
    protected function logResponse(\Zend\Stdlib\ResponseInterface $response)
    {
        $content = $response->getContent();
        if (strlen($content) > 1000) {
            $content = substr($content, 0, 1000) . '...';
        }

        if ($response instanceof \Zend\Console\Response) {
            $priority = $response->getErrorLevel() === 0 ? \Zend\Log\Logger::DEBUG : \Zend\Log\Logger::ERR;
            Logger::log(
                $priority,
                'CLI Response Sent',
                ['errorLevel' => $response->getErrorLevel(), 'content' => $content]
            );
        }

        if ($response instanceof \Zend\Http\PhpEnvironment\Response) {
            if (
                empty($content)
                && !in_array($response->getStatusCode(), self::$allowEmptyStatusCodes, true)
            ) {
                // Response should never be empty, this is a symptom that the backend has gone wrong
                // Except if the status code is 204 No content and 206 - Partial content (Download)
                Logger::err('API Response is empty');
            }
            Logger::logResponse(
                $response->getStatusCode(),
                'API Response Sent',
                ['status' => $response->getStatusCode(), 'content' => $content]
            );
        }
    }
}
