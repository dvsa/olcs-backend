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
 * OperatorApprove
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class OperatorApprove extends AbstractCommandHandler implements TransactionedInterface, EmailAwareInterface
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

        $tma->setTmApplicationStatus(
            $this->getRepo()->getRefdataReference(TransportManagerApplication::STATUS_OPERATOR_SIGNED)
        );
        $this->getRepo()->save($tma);

        $this->sendConfirmedEmail($tma);

        $this->result->addMessage("Transport Manager Application ID {$tma->getId()} operator approved");

        return $this->result;
    }

    /**
     * Send an email to the TM
     *
     * @param TransportManagerApplication $tma
     */
    private function sendConfirmedEmail(TransportManagerApplication $tma)
    {
        $message = new \Dvsa\Olcs\Email\Data\Message(
            $tma->getTransportManager()->getHomeCd()->getEmailAddress(),
            'email.transport-manager-confirmed.subject'
        );
        $message->setTranslateToWelsh($tma->getApplication()->getLicence()->getTranslateToWelsh());

        $this->sendEmailTemplate(
            $message,
            'transport-manager-confirmed',
            [
                'operatorName' => $tma->getApplication()->getLicence()->getOrganisation()->getName(),
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
