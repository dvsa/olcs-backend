<?php

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot;

use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
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

        // initialise the translator
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $sm->get('translator');
        $translator->setLocale('en_GB');
        $translator->setFallbackLocale('en_GB');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/../config/language', '%s.php', 'snapshot');

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
