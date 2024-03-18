<?php

namespace Dvsa\OlcsTest\Cli\Command\Batch;

use Dvsa\Olcs\Cli\Domain\Query\CompaniesHouse\Organisations;
use Dvsa\Olcs\Cli\Command\Batch\EnqueueCompaniesHouseCompareCommand;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Enqueue;

class EnqueueCompaniesHouseCompareCommandTest extends AbstractBatchCommandCases
{
    use QueryHandlerExceptionTestsTrait;

    protected function getCommandClass()
    {
        return EnqueueCompaniesHouseCompareCommand::class;
    }

    protected function getCommandName()
    {
        return 'batch:enqueue-companies-house-compare';
    }

    protected function getCommandDTOs()
    {
        return [
            Enqueue::create([
                'messageData' => ['org1', 'org2'],
                'queueType' => 'Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\CompanyProfile',
                'messageType' => 'Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\CompanyProfile',
            ]),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockQueryHandlerManager->method('handleQuery')
            ->willReturnCallback(function ($query) {
                $this->assertInstanceOf(Organisations::class, $query, "Query is not of type Organisation");
                return ['org1', 'org2'];
            });
    }
}
