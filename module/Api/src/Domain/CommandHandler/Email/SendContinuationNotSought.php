<?php

/**
 * Send Continuation Not Sought Email
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;


/**
 * Send Continuation Not Sought Email
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class SendContinuationNotSought extends AbstractCommandHandler implements EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    protected $repoServiceName = 'TransportManagerApplication';
    protected $extraRepos = ['SystemParameter'];

    const DATE_FORMAT = 'd/m/Y';
    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $tma \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication */
        $licences = $command->getLicences()

        $endDate = $command->getDate();
        $time = strtotime($endDate);
        $startDate = clone $endData;
        $startDate->sub(new \DateInterval('P1M')

        $to = $this->getRepo('SystemParameter')->fetchValue(SystemParameter::CNS_EMAIL_LIST);

        $subject = vsprintf(
            $this->translate('email.cns.subject'),
            [$startDate->format(self::DATE_FORMAT), $endDate->format(self::DATE_FORMAT)]
        );

        $message = new \Dvsa\Olcs\Email\Data\Message($to, $subject);

        $this->sendEmailTemplate(
            $message,
            'continuation-not-sought',
            compact('startDate', 'endDate', 'licences')
        );

        $result = new Result();

        $result->addMessage('Continuation Not Sought email sent');

        return $result;
    }
}
