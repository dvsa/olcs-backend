<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Submit
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Submit extends AbstractCommandHandler implements TransactionedInterface, EmailAwareInterface
{
    use EmailAwareTrait;

    protected $repoServiceName = 'TransportManagerApplication';

    protected $extraRepos = ['TransportManager'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $tma TransportManagerApplication */
        if ($command->getVersion()) {
            $tma = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        } else {
            $tma = $this->getRepo()->fetchUsingId($command);
        }

        // next status depends on whether TM is the owner
        $nextStatus = ($tma->getIsOwner() === 'Y') ? TransportManagerApplication::STATUS_OPERATOR_SIGNED :
            TransportManagerApplication::STATUS_TM_SIGNED;
        $tma->setTmApplicationStatus($this->getRepo()->getRefdataReference($nextStatus));
        $this->getRepo()->save($tma);

        if ($nextStatus === TransportManagerApplication::STATUS_TM_SIGNED) {
            $this->sendSubmittedEmail($tma);
        } elseif ($tma->getTransportManager()->getTmType() === null) {
            $this->updateTmType($tma->getTransportManager(), $tma->getTmType());
        }

        $this->result->addMessage("Transport Manager Application ID {$tma->getId()} submitted");

        return $this->result;
    }

    /**
     * Send an email to the Organisations admins and TMA creator
     *
     * @param TransportManagerApplication $tma tma
     *
     * @return void
     */
    private function sendSubmittedEmail(TransportManagerApplication $tma)
    {
        $recipients = $this->getRecipients($tma);

        if (!empty($recipients)) {
            $emailData = [
                'tmFullName' => $tma->getTransportManager()->getHomeCd()->getPerson()->getFullName(),
                'licNo' => $tma->getApplication()->getLicence()->getLicNo(),
                'applicationId' => $tma->getApplication()->getId(),
                'tmaUrl' => sprintf(
                    'http://selfserve/%s/%d/transport-managers/details/%d/',
                    $tma->getApplication()->getIsVariation() ? 'variation' : 'application',
                    $tma->getApplication()->getId(),
                    $tma->getId()
                ),
            ];

            foreach ($recipients as $user) {
                $message = new \Dvsa\Olcs\Email\Data\Message(
                    $user->getContactDetails()->getEmailAddress(),
                    'email.transport-manager-submitted-form.subject'
                );
                $message->setTranslateToWelsh($user->getTranslateToWelsh());

                $this->sendEmailTemplate(
                    $message,
                    'transport-manager-submitted-form',
                    $emailData
                );
            }
        }
    }

    /**
     * Get list of users who should receive the email
     *
     * @param TransportManagerApplication $tma tma
     *
     * @return array Array of User indexed by id
     */
    private function getRecipients(TransportManagerApplication $tma)
    {
        $users = [];

        foreach ($tma->getApplication()->getLicence()->getOrganisation()->getAdminOrganisationUsers() as $orgUser) {
            $user = $orgUser->getUser();

            $users[$user->getId()] = $user;
        }

        if ($tma->getCreatedBy()) {
            // add TMA creator to the list
            $users[$tma->getCreatedBy()->getId()] = $tma->getCreatedBy();
        }

        return $users;
    }

    /**
     * Update tm type
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManager $tm     transport manager
     * @param string                                    $tmType transport manager type
     *
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @return void
     */
    protected function updateTmType(TransportManager $tm, $tmType)
    {
        $tm->setTmType($this->getRepo()->getRefdataReference($tmType));
        $this->getRepo('TransportManager')->save($tm);
    }
}
