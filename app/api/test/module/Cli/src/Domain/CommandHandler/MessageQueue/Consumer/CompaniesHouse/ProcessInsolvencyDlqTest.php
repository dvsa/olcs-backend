<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Email\SendFailedOrganisationsList;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\MessageFailures as MessageFailuresRepo;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Entity\MessageFailures;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvencyDlq as ProcessInsolvencyDlqCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvencyDlq;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Dvsa\Olcs\Queue\Service\Queue;
use Mockery as m;

class ProcessInsolvencyDlqTest extends CompaniesHouseConsumerTestCase
{
    protected $config = [
        'message_queue' => [
            'ProcessInsolvencyDlq_URL' => 'process_insolvency_dlq_url'
        ],
        'company_house_dlq' => [
            'notification_email_address' => 'test@test.com'
        ]
    ];

    public function setUp(): void
    {
        $this->sut = new ProcessInsolvencyDlq();
        $this->mockRepo('MessageFailures', MessageFailuresRepo::class);
        $this->mockRepo('Organisation', OrganisationRepo::class);
    }

    public function testHandleCommandSendsEmail()
    {
        $this->setUpServices();

        $this->repoMap['MessageFailures']
            ->shouldReceive('saveOnFlush')
            ->times(4);

        $this->repoMap['MessageFailures']
            ->shouldReceive('flushAll')
            ->once();

        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->andReturn([m::mock(Organisation::class)])
            ->times(4)
            ->getMock();

        $result = new Result();
        $result->addMessage('Email sent');
        $this->expectedSideEffect(
            SendFailedOrganisationsList::class,
            [
                'organisationNumbers' => ['0000', '1111', '2222', '3333'],
                'emailAddress' => 'test@test.com',
                'emailSubject' => 'Companies House Insolvency process failure - list of those that failed'
            ],
            $result
        );

        $command = ProcessInsolvencyDlqCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            'Email sent'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    public function testHandleCommandRemovesDuplicateOrganisationNumbersFromEmail()
    {
        $this->setUpServicesWithDuplicateQueueEntries();

        $this->repoMap['MessageFailures']
            ->shouldReceive('saveOnFlush')
            ->times(2);

        $this->repoMap['MessageFailures']
            ->shouldReceive('flushAll')
            ->once();

        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->andReturn([m::mock(Organisation::class)])
            ->times(2)
            ->getMock();

        $result = new Result();
        $result->addMessage('Email sent');
        $this->expectedSideEffect(
            SendFailedOrganisationsList::class,
            [
                'organisationNumbers' => ['0000'],
                'emailAddress' => 'test@test.com',
                'emailSubject' => 'Companies House Insolvency process failure - list of those that failed'
            ],
            $result
        );

        $command = ProcessInsolvencyDlqCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            'Email sent'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    public function testHandleCommandWhenQueueIsEmpty()
    {
        $this->setupServicesWithEmptyQueue();

        $command = ProcessInsolvencyDlqCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = ['No messages to process'];
        $flags = ['no_messages' => true];

        $this->assertEquals($messages, $response->getMessages());
        $this->assertEquals($flags, $response->getFlags());
    }

    protected function getMockQueueService()
    {
        $queueService = m::mock(Queue::class);

        $firstFetchResult = [
            [
                'Body' => '0000',
                'ReceiptHandle' => 1
            ],
            [
                'Body' => '1111',
                'ReceiptHandle' => 1
            ]
        ];

        $secondFetchResult = [
            [
                'Body' => '2222',
                'ReceiptHandle' => 1
            ],
            [
                'Body' => '3333',
                'ReceiptHandle' => 1
            ]
        ];

        $thirdFetchResult = null;

        $queueService->shouldReceive('fetchMessages')
            ->with('process_insolvency_dlq_url', 10, 1)
            ->andReturn(
                $firstFetchResult,
                $secondFetchResult,
                $thirdFetchResult
            )
            ->times(3);

        $queueService->shouldReceive('deleteMessage')
            ->with('process_insolvency_dlq_url', 1)
            ->times(4);

        return $queueService;
    }

    protected function setUpServices()
    {
        $this->mockedSmServices = [
            Queue::class => $this->getMockQueueService(),
            MessageBuilder::class => m::mock(MessageBuilder::class),
            'Config' => $this->config
        ];
        $this->setupService();
    }

    protected function getEmptyQueueService()
    {
        $queueService = m::mock(Queue::class);

        $queueService->shouldReceive('fetchMessages')
            ->with('process_insolvency_dlq_url', 10, 1)
            ->andReturn([])
            ->once();

        return $queueService;
    }

    protected function setupServicesWithEmptyQueue()
    {
        $this->mockedSmServices = [
            Queue::class => $this->getEmptyQueueService(),
            MessageBuilder::class => m::mock(MessageBuilder::class),
            'Config' => $this->config
        ];
        $this->setupService();
    }

    protected function setUpServicesWithDuplicateQueueEntries()
    {
        $this->mockedSmServices = [
            Queue::class => $this->getMockQueueServiceWithDuplicateEntries(),
            MessageBuilder::class => m::mock(MessageBuilder::class),
            'Config' => $this->config
        ];
        $this->setupService();
    }

    protected function getMockQueueServiceWithDuplicateEntries()
    {
        $queueService = m::mock(Queue::class);

        $firstFetchResult = [
            [
                'Body' => '0000',
                'ReceiptHandle' => 1
            ],
            [
                'Body' => '0000',
                'ReceiptHandle' => 1
            ]
        ];

        $secondFetchResult = null;

        $queueService->shouldReceive('fetchMessages')
            ->with('process_insolvency_dlq_url', 10, 1)
            ->andReturn(
                $firstFetchResult,
                $secondFetchResult
            )
            ->times(2);

        $queueService->shouldReceive('deleteMessage')
            ->with('process_insolvency_dlq_url', 1)
            ->times(2);

        return $queueService;
    }
}
