<?php

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot;

use Dvsa\Olcs\Snapshot\Service\Translator\MissingTranslationProcessor;
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
        // initialise the translator
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->setLocale('en_GB');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/../config/language', '%s.php', 'snapshot');

        $events = $e->getApplication()->getEventManager();

        $missingTranslationProcessor = new MissingTranslationProcessor(
        // Inject the renderer and template resolver
            $e->getApplication()->getServiceManager()->get('ViewRenderer'),
            $e->getApplication()->getServiceManager()->get('Zend\View\Resolver\TemplatePathStack')
        );

        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            array($missingTranslationProcessor, 'processEvent')
        );

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
