<?php

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot;

use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
use Zend\Cache\Storage\Adapter\Redis;
use Zend\I18n\Translator\Translator;

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Module
{
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        /**
         * @var Translator $translator
         * @var Redis      $cache
         */
        $cache = $sm->get(Redis::class);
        $translator = $sm->get('translator');
        $translator->setCache($cache);

        $events = $e->getApplication()->getEventManager();

        /** @var  MissingTranslationProcessor $missingTranslationProcessor */
        $missingTranslationProcessor = $sm->get('Utils\MissingTranslationProcessor');
        $missingTranslationProcessor->attach($events);

        $translator->enableEventManager();
        $translator->setEventManager($events);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }
}
