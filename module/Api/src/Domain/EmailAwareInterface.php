<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * Email Aware Interface
 */
interface EmailAwareInterface
{
    /**
     * @param \Dvsa\Olcs\Email\Service\Client $service
     */
    public function setEmailService(\Dvsa\Olcs\Email\Service\Client $service);

    /**
     * @return \Dvsa\Olcs\Email\Service\Client
     */
    public function getEmailService();

    /**
     * @param \Dvsa\Olcs\Email\Service\TemplateRenderer $service
     */
    public function setTemplateRendererService(\Dvsa\Olcs\Email\Service\TemplateRenderer $service);

    /**
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function getTemplateRendererService();
}
