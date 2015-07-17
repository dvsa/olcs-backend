<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Email\Service\Client;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Email Aware
 */
trait EmailAwareTrait
{
    /**
     * @var Client
     */
    protected $emailService;

    /**
     * @var TemplateRenderer
     */
    protected $templateRendererService;

    /**
     * @param Client $service
     */
    public function setEmailService(Client $service)
    {
        $this->emailService = $service;
    }

    /**
     * @return Client
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

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
        return $this->getEmailService()->sendEmail($message);
    }

    /**
     * Send an email in a HTML template
     *
     * @param Message $message
     * @param string $template
     * @param array $variables
     * @param string $layout
     *
     * @return true
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function sendEmailTemplate(Message $message, $template, array $variables = [], $layout = null)
    {
        $this->getTemplateRendererService()->renderBody($message, $template, $variables, $layout);
        return $this->sendEmail($message);
    }
}
