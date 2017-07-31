<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;

/**
 * Send  PSV OperatorList report
 */
final class SendPsvOperatorListReport extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    const EMAIL_TEMPLATE = 'report-psv-operator-list';
    const EMAIL_SUBJECT = 'email.notification.subject';

    protected $repoServiceName = 'SystemParameter';

    /**
     * @param \Dvsa\Olcs\Api\Domain\Command\Email\SendPsvOperatorListReport $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var SystemParameterRepo $systemParametersRepo */
        $systemParametersRepo = $this->getRepo();
        $email = $systemParametersRepo->fetchValue(SystemParameter::PSV_REPORT_EMAIL_LIST);

        if (is_null($email)) {
            throw new \InvalidArgumentException('No email address specified in system parameters for the PSV Report');
        }

        $message = new Message($email, self::EMAIL_SUBJECT);
        $message->setDocs([$command->getId()]);

        $this->sendEmailTemplate($message,self::EMAIL_TEMPLATE);

        $result = new Result();
        $result->addId('document', $command->getId());
        $result->addMessage('PSV Operator list sent to: ' . $email);
        return $result;
    }
}
