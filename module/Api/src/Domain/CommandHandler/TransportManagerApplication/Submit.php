<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;

/**
 * Submit
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Submit extends AbstractCommandHandler implements TransactionedInterface, EmailAwareInterface
{
    use EmailAwareTrait;

    protected $repoServiceName = 'TransportManagerApplication';

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
        }

        $this->result->addMessage("Transport Manager Application ID {$tma->getId()} submitted");

        return $this->result;
    }

    /**
     * Send an email to the Organisations admins and TMA creator
     *
     * @param TransportManagerApplication $tma
     */
    private function sendSubmittedEmail(TransportManagerApplication $tma)
    {
        // get all org admin email addresss
        $toEmailAddresses = $tma->getApplication()->getLicence()->getOrganisation()->getAdminEmailAddresses();

        // Created by, might not have a value
        if ($tma->getCreatedBy()) {
            // check if the TMA creator email address is already in the "to list", if not then add it
            $tmaCreatorEmailAddress = $tma->getCreatedBy()->getContactDetails()->getEmailAddress();
            if (!empty($tmaCreatorEmailAddress) && array_search($tmaCreatorEmailAddress, $toEmailAddresses) === false) {
                $toEmailAddresses[] = $tmaCreatorEmailAddress;
            }
        }

        foreach ($toEmailAddresses as $to) {
            $message = new \Dvsa\Olcs\Email\Data\Message($to, 'email.transport-manager-submitted-form.subject');
            $message->setTranslateToWelsh($tma->getApplication()->getLicence()->getTranslateToWelsh());
            $this->sendEmailTemplate(
                $message,
                'transport-manager-submitted-form',
                [
                    'tmFullName' => $tma->getTransportManager()->getHomeCd()->getPerson()->getFullName(),
                    'licNo' => $tma->getApplication()->getLicence()->getLicNo(),
                    'applicationId' => $tma->getApplication()->getId(),
                    'tmaUrl' => sprintf(
                        'http://selfserve/application/%d/transport-managers/details/%d/',
                        $tma->getApplication()->getId(),
                        $tma->getId()
                    ),
                ]
            );
        }
    }
}
