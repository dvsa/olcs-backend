<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;

/**
 * Send  PSV OperatorList report
 */
final class SendPsvOperatorListReport extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    protected $repoServiceName = 'SystemParameter';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var SystemParameter $systemParametersRepo */
        $systemParametersRepo = $this->getRepo();
        $email = $systemParametersRepo->fetchValue(SystemParameter::PSV_REPORT_EMAIL_LIST);

        if (is_null($email)) {
            throw new \InvalidArgumentException('No email address specified in system parameters for the PSV Report');
        }

        $message = new \Dvsa\Olcs\Email\Data\Message(
            $email,
            'email.notification.subject'
        );

        $emailParameters = $command->getArrayCopy();

        $this->sendEmailTemplate(
            $message,
            'report-psv-operator-list',
            [
                'filename' => $emailParameters['document_store_id']
            ]
        );

        $result = new Result();
        $result->addId('document', $command->getId());
        $result->addMessage('PSV Operator list sent to: ' . $email);
        return $result;
    }
}
