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

    /**
     * @return array
     * @codeCoverageIgnore No value in testing a method which returns config.
     */
    public function getConfig()
    {
        return array(
            'email' => array(
                'http' => array(),
                'client' => array(
                    'baseuri' => 'http://olcs-email/',
                    'from_name' => 'OLCS do not reply',
                    'from_email' => 'donotreply@otc.gsi.gov.uk',
                    'selfserve_uri' => 'http://olcs-selfserve/',
                )
            ),
            'service_manager' => array(
                'factories' => array(
                    Service\Client::class => Service\ClientFactory::class,
                    Service\TemplateRenderer::class => Service\TemplateRendererFactory::class,
                ),
                'aliases' => [
                    'translator' => 'MvcTranslator',
                ],
            ),
            'view_manager' => array(
                'template_map' => array(
                    'layout/email' => __DIR__ . '/../view/layout/email.phtml',
                ),
                'template_path_stack' => array(
                    'email' => __DIR__ . '/../view/email',
                )
            ),
        );
    }

    /**
     * Empty on purpose to defer loading to composer
     * @codeCoverageIgnore No value in testing an empty method
     */
    public function getAutoloaderConfig()
    {
    }
}
