<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Send ECMT short term app submitted email
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SendEcmtShortTermAppSubmitted extends AbstractEcmtShortTermEmailHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use PermitEmailTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';
    protected $template = 'ecmt-short-term-app-submitted';
    protected $subject = 'email.ecmt.short.term.response.subject';

    /**
     * Get template variables
     *
     * @param IrhpApplication $recordObject
     *
     * @return array
     */
    protected function getTemplateVariables($recordObject): array
    {
        return [
            'applicationRef' => $recordObject->getApplicationRef(),
            'permitsUrl' => 'http://selfserve/permits',
            'appUrl' => 'http://selfserve/',
        ];
    }
}
