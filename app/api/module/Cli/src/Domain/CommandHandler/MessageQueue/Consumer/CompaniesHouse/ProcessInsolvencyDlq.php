<?php declare(strict_types=1);

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Email\SendInsolvencyFailureList;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\MessageFailures;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\AbstractConsumer;
use Dvsa\Olcs\CompaniesHouse\Service\Exception\ServiceException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class ProcessInsolvencyDlq extends AbstractConsumer
{
    use QueueAwareTrait;
    use ConfigAwareTrait;

    const MAX_NUMBER_OF_MESSAGES = 10;
    const EMAIL_SUBJECT = 'Companies House Insolvency process failure - list of those that failed';

    protected $repoServiceName = 'MessageFailures';

    protected $extraRepos = ['Organisation'];

    /**
     * @param CommandInterface $command
     *
     * @return Result
     * @throws RuntimeException
     * @throws ServiceException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $organisationRepo = $this->getRepo('Organisation');
        $messageFailuresRepo = $this->getRepo('MessageFailures');
        $organisationIds = [];
        $emailAddress = $this->getConfig()['company_house_dlq']['notification_email_address'];

        while($messages = $this->fetchMessages(self::MAX_NUMBER_OF_MESSAGES)) {

            foreach ($messages as $message) {

                $companyNumber = $message['Body'];
                $organisations = $organisationRepo->getByCompanyOrLlpNo($companyNumber);

                $messageFailure = new MessageFailures();
                $messageFailure->setOrganisation($organisations[0]);
                $messageFailure->setQueueType(ProcessInsolvency::class);
                $messageFailuresRepo->saveOnFlush($messageFailure);

                $this->deleteMessage($message);
                $organisationIds[] = $companyNumber;
            }
        }

        $messageFailuresRepo->flushAll();

        //Use this for email - instead of the emailQueue method
        $emailParams = [
            'organisationIds' => $organisationIds,
            'emailAddress' => $emailAddress,
            'emailSubject' => self::EMAIL_SUBJECT
        ];
        $this->result->merge($this->handleSideEffect(SendInsolvencyFailureList::create($emailParams)));

//TODO
        // Needs to do this
//        if (is_null($messages)) {
//            $this->noMessages();
//            return $this->result;
//        }

        return $this->result;
    }
}
