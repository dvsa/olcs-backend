<?php

namespace Dvsa\Olcs\Email;

/**
 * Email Module
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Module
{
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        // initialise the translator
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->setLocale('en_GB');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/../config/language', '%s.php', 'email');
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Empty on purpose to defer loading to composer
     * @codeCoverageIgnore No value in testing an empty method
     */
    public function getAutoloaderConfig()
    {
    }
}
