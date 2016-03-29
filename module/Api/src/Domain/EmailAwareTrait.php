<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Zend\Json\Json as ZendJson;

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
    public function sendEmailTemplate(Message $message, $template, array $variables = [], $layout = null)
    {
        $this->getTemplateRendererService()->renderBody($message, $template, $variables, $layout);
        return $this->sendEmail($message);
    }

    /**
     * Adds an email to the queue
     *
     * @param string $cmdClass
     * @param array $cmdData
     * @param int $entityId
     * @param string|null $processAfterDate
     * @return CreateQueue
     */
    public function emailQueue($cmdClass, array $cmdData, $entityId, $processAfterDate = null)
    {
        $options =                     [
            'commandClass' => $cmdClass,
            'commandData' => $cmdData,
        ];

        return CreateQueue::create(
            [
                'entityId' => $entityId,
                'type' => Queue::TYPE_EMAIL,
                'status' => Queue::STATUS_QUEUED,
                'options' => ZendJson::encode($options),
                'processAfterDate' => $processAfterDate
            ]
        );
    }
}
