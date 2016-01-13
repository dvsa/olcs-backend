<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * Email Aware Interface
 */
interface EmailAwareInterface
{
    /**
     * @param \Dvsa\Olcs\Email\Service\TemplateRenderer $service
     */
    public function setTemplateRendererService(\Dvsa\Olcs\Email\Service\TemplateRenderer $service);

    /**
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function getTemplateRendererService();
}
