<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

class SendAmendTmApplication extends AbstractEmailHandler
{
    protected $repoServiceName = 'TransportManagerApplication';

    protected $template = 'transport-manager-complete-digital-form';

    protected $subject = 'email.transport-manager-complete-digital-form.subject';

    protected $resultMessage = 'Transport Manager Application email sent';

    /**
     * @param object $tma
     *
     * @return array
     */
    protected function getRecipients($tma): array
    {
        /* @var $tma \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication */
        return [
            'to' => $tma->getTransportManager()->getUsers()->first()->getContactDetails()->getEmailAddress(),
            'cc' => [],
            'bcc' => []
        ];
    }

    /**
     * Returns variables for templates
     *
     * @param object $tma
     *
     * @return array
     */
    protected function getTemplateVariables($tma)
    {
        /* @var $tma \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication */
        return [
            'organisation' => $tma->getApplication()->getLicence()->getOrganisation()->getName(),
            'reference' => $tma->getApplication()->getLicence()->getLicNo() . '/' . $tma->getApplication()->getId(),
            'username' => $username = $tma->getTransportManager()->getUsers()->isEmpty() ? 'not registered' :
                $tma->getTransportManager()->getUsers()->first()->getLoginId(),
            'isNi' => $tma->getApplication()->getNiFlag() === 'Y',
            'signInLink' => sprintf(
                'http://selfserve/%s/%d/transport-managers/details/%d/edit-details/',
                ($tma->getApplication()->getIsVariation()) ? 'variation' : 'application',
                $tma->getApplication()->getId(),
                $tma->getId()
            )
        ];
    }

    protected function getTranslateToWelsh($tma): string
    {
        /* @var $tma \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication */
        return $tma->getApplication()->getLicence()->getTranslateToWelsh();
    }



}
