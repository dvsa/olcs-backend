<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Exception\MissingEmailException;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\User\User;
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
     *
     * @return void
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
     * @param Message      $message
     * @param string|array $template
     * @param array        $variables
     * @param string       $layout
     *
     * @return true
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function sendEmailTemplate(Message $message, $template, array $variables = [], $layout = 'default')
    {
        $this->getTemplateRendererService()->renderBody($message, $template, $variables, $layout);
        return $this->sendEmail($message);
    }

    /**
     * Returns a list of recipient addresses for an ecmt permit application email
     *
     * @param EcmtPermitApplication $application the permit application
     *
     * @return array
     * @throws MissingEmailException
     */
    public function recipientsForPermitApplication(EcmtPermitApplication $application): array
    {
        return $this->organisationRecipients(
            $application->getLicence()->getOrganisation(),
            $application->getCreatedBy()
        );
    }

    /**
     * In theory (although probably not in practice) both the user and the organisation emails could be empty
     * Need to decide what to do in those situations (throw an exception here?)
     *
     * @param Organisation $organisation
     * @param User         $user
     *
     * @return array
     * @throws MissingEmailException
     */
    public function organisationRecipients(Organisation $organisation, ?User $user): array
    {
        $toEmail = '';
        $orgEmailAddresses = $organisation->getAdminEmailAddresses();

        //on rare occasions a user may have been soft deleted, or may not have contact details
        if ($user instanceof User) {
            $contactDetails = $user->getContactDetails();

            if ($contactDetails instanceof ContactDetails) {
                $toEmail = $contactDetails->getEmailAddress();
            }
        }

        if (empty($toEmail) && !empty($orgEmailAddresses)) {
            $toEmail = $orgEmailAddresses[0];
            unset($orgEmailAddresses[0]);
        }

        if (empty($toEmail)) {
            throw new MissingEmailException(MissingEmailException::MSG_NO_ORG_EMAIL);
        }

        return [
            'to' => $toEmail,
            'cc' => $orgEmailAddresses
        ];
    }
}
