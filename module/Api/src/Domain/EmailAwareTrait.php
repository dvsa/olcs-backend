<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * Email Aware
 */
trait EmailAwareTrait
{
    /**
     * @var \Dvsa\Olcs\Api\Domain\Dvsa\Olcs\Email\Service\Client
     */
    protected $emailService;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Dvsa\Olcs\Email\Service\Client
     */
    protected $templateRendererService;

    /**
     * @param \Dvsa\Olcs\Api\Domain\Dvsa\Olcs\Email\Service\Client $service
     */
    public function setEmailService(\Dvsa\Olcs\Email\Service\Client $service)
    {
        $this->emailService = $service;
    }

    /**
     * @return \Dvsa\Olcs\Email\Service\Client
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * @param \Dvsa\Olcs\Email\Service\TemplateRenderer $service
     */
    public function setTemplateRendererService(\Dvsa\Olcs\Email\Service\TemplateRenderer $service)
    {
        $this->templateRendererService = $service;
    }

    /**
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function getTemplateRendererService()
    {
        return $this->templateRendererService;
    }

    /**
     * Send an email
     *
     * @param \Dvsa\Olcs\Email\Data\Message $message
     *
     * @return true
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function sendEmail(\Dvsa\Olcs\Email\Data\Message $message)
    {
        return $this->getEmailService()->sendEmail($message);
    }

    /**
     * Send an email in a HTML template
     *
     * @param \Dvsa\Olcs\Email\Data\Message $message
     * @param string $template
     * @param array $variables
     *
     * @return true
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function sendEmailTemplate(\Dvsa\Olcs\Email\Data\Message $message, $template, array $variables = [])
    {
        $this->getTemplateRendererService()->renderBody($message, $template, $variables);
        return $this->sendEmail($message);
    }
}
