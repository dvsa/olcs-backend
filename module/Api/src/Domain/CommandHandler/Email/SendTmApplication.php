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

        if (!$tma->getTransportManager()->getUsers()->isEmpty()) {
            // got user linked to the TM
            $user = $tma->getTransportManager()->getUsers()->first();

            $to = $user->getContactDetails()->getEmailAddress();
            $username = $user->getLoginId();

            // use user's preference
            $translateToWelsh = $user->getTranslateToWelsh();
        } else {
            // no user linked
            $to = $tma->getTransportManager()->getHomeCd()->getEmailAddress();
            $username = 'not registered';

            // use licence settings
            $translateToWelsh = $tma->getApplication()->getLicence()->getTranslateToWelsh();
        }

        $message = new \Dvsa\Olcs\Email\Data\Message(
            $to,
            'email.transport-manager-complete-digital-form.subject'
        );
        $message->setTranslateToWelsh($translateToWelsh);

        $this->sendEmailTemplate(
            $message,
            'transport-manager-complete-digital-form',
            [
                'organisation' => $tma->getApplication()->getLicence()->getOrganisation()->getName(),
                'reference' => $tma->getApplication()->getLicence()->getLicNo() .'/'. $tma->getApplication()->getId(),
                'username' => $username,
                'isNi' => $tma->getApplication()->getNiFlag() === 'Y',
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
