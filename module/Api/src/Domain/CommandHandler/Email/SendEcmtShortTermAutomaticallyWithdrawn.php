<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;

/**
 * Send confirmation of ECMT short term app being automatically withdrawn
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SendEcmtShortTermAutomaticallyWithdrawn extends AbstractEmailHandler
{
    use PermitEmailTrait;

    protected $repoServiceName = 'IrhpApplication';
    protected $template = 'ecmt-short-term-automatically-withdrawn';
    protected $subject = 'email.ecmt.short.term.automatically.withdrawn.subject';

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
            'paymentDeadlineNumDays' => '10', // TODO - OLCS-21979
        ];
    }
}
