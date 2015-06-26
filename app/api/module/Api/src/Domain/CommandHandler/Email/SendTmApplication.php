<?php

/**
 * Send Transport Manager Application Email
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Send Transport Manager Application Email
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class SendTmApplication extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    protected $repoServiceName = 'TransportManagerApplication';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $tma \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication */
        $tma = $this->getRepo()->fetchUsingId($command);

        $message = new \Dvsa\Olcs\Email\Data\Message(
            $tma->getTransportManager()->getHomeCd()->getEmailAddress(),
            'email.transport-manager-complete-digital-form.subject'
        );
        $message->setTranslateToWelsh($tma->getApplication()->getLicence()->getTranslateToWelsh());

        $this->sendEmailTemplate(
            $message,
            'transport-manager-complete-digital-form',
            [
                'name' => $tma->getTransportManager()->getHomeCd()->getPerson()->getForename(),
                'organisation' => $tma->getApplication()->getLicence()->getOrganisation()->getName(),
                'reference' => $tma->getApplication()->getLicence()->getLicNo() .'/'. $tma->getApplication()->getId(),
                'signInLink' => sprintf(
                    'http://selfserve/%s/%d/transport-managers/details/%d/edit-details/',
                    ($tma->getApplication()->getIsVariation()) ? 'variation' : 'application',
                    $tma->getApplication()->getId(),
                    $tma->getId()
                )
            ]
        );

        $result = new Result();
        $result->addId('transportManagerApplication', $tma->getId());
        $result->addMessage('Transport Manager Application email sent');
        return $result;
    }
}
