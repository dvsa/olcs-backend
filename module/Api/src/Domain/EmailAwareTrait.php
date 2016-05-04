<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Email Aware
 */
trait EmailAwareTrait
{
    /**
     * @var TemplateRenderer
     */
    protected $templateRendererService;

    /**
     * @param TemplateRenderer $service
     */
    public function setTemplateRendererService(TemplateRenderer $service)
    {
        $this->templateRendererService = $service;
    }

    /**
     * @return TemplateRenderer
     */
    public function getTemplateRendererService()
    {
        return $this->templateRendererService;
    }

    /**
     * Send an email
     *
     * @param Message $message
     *
     * @return true
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function sendEmail(Message $message)
    {
        return $this->handleSideEffect($message->buildCommand());
    }

    /**
     * Send an email in a HTML template
     *
     * @param Message $message
     * @param string|array $template
     * @param array $variables
     * @param string $layout
     *
     * @return true
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function sendEmailTemplate(Message $message, $template, array $variables = [], $layout = 'default')
    {
        $this->getTemplateRendererService()->renderBody($message, $template, $variables, $layout);
        return $this->sendEmail($message);
    }
}
