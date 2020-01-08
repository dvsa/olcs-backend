<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\Compare;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\AbstractConsumer;
use Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\ProcessInsolvency;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class CompanyProfile extends AbstractConsumer
{
    protected $repoServiceName = 'CompaniesHouseCompany';

    /**
     * @param CommandInterface $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $messages = $this->fetchMessages(1);

        if (is_null($messages)) {
            $this->noMessages();
            return $this->result;
        }

        $companyNumber = $messages[0]['Body'];
        $isProcessed = false;
        $this->result->merge($this->handleSideEffect(Compare::create(['companyNumber' => $companyNumber])));

        /** @var CompaniesHouseCompany $companiesHouseCompany */
        try {
            $companiesHouseCompany = $this->getRepo()->getLatestByCompanyNumber($companyNumber);
            $isProcessed = $companiesHouseCompany->getInsolvencyProcessed();

        }
        catch(NotFoundException $notFoundException)
        {
            //company not found halts the consumer logic should be that it drops into DL queue

        }

        if ($this->result->getFlag('isInsolvent') && !$isProcessed) {
            $insolvencyMessage = $this->messageBuilderService->buildMessage(
                ['companyOrLlpNo' => $companyNumber],
                ProcessInsolvency::class,
                $this->queueConfig
            );
            $this->queueService->sendMessage($insolvencyMessage->toArray());
            $this->result->addMessage('Queued company ' . $companyNumber . ' for processing insolvency');
        }

        $this->deleteMessage($messages[0]);

        return $this->result;
    }
}
