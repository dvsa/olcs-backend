<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\MessageQueue\Consumer\TransXChange;

use Aws\S3\S3Client;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\UpdateTxcInboxPdf as UpdateTxcInboxPdfCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Rbac\IdentityProviderInterface;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\TransXChange\TransXChangeConsumer as TransXChangeConsumerCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\TransXChange\TransXChangeConsumer;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Dvsa\Olcs\Queue\Service\Queue;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Exception;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Validator\ValidatorPluginManager;
use Mockery as m;
use Olcs\Logging\Log\Logger;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;
use RuntimeException;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TransXChangeConsumerTest extends CommandHandlerTestCase
{
    protected array $config = [
        'message_queue' => [
            'TransXChangeConsumer_URL' => 'QUEUE_URI',
        ],
        'awsOptions' => [
            'region' => 'eu-west-1',
            'global' => [],
            's3' => [],
        ],
        'ebsr' => [
            'output_s3_bucket' => 'txc-local-output',
            'txc_consumer_role_arn' => 'arn:aws:iam::000000000000:role/txc-local-consumer-role',
            'max_messages_per_run' => 100,
        ]
    ];

    /**
     * @var m\MockInterface|Xsd
     */
    private m\MockInterface $xmlValidator;

    /**
     * @var m\MockInterface|MapXmlFile
     */
    private m\MockInterface $xmlFilter;

    /**
     * @var m\MockInterface|ParseXmlString
     */
    private m\MockInterface $xmlParser;

    /**
     * @var m\MockInterface|S3Client
     */
    private m\MockInterface $s3Client;

    public function setUp(): void
    {
        $this->sut = new TransXChangeConsumer();
        $this->mockRepo('EbsrSubmission', EbsrSubmission::class);

        $logWriter = new \Laminas\Log\Writer\Mock();
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($logWriter);

        Logger::setLogger($logger);
    }

    public function testEmptyQueue()
    {
        $this->setupMocks();

        $this->mockedSmServices[Queue::class]->shouldReceive('fetchMessages')->andReturn([]);

        $command = TransXChangeConsumerCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            'No messages to process'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    public function testMaxMessagesPerRun(): void
    {
        $this->config['ebsr']['max_messages_per_run'] = 20;

        $this->setupMocks();

        $messages = array_fill(0, 7, $this->createQueueMessage('Timetable', 'ExampleTimetable.xml'));

        // Ensure that all types of requests are ran.
        array_push(
            $messages,
            $this->createQueueMessage(TransXChangeConsumer::TYPES['Timetable'], 'ExampleTimetable.xml'),
            $this->createQueueMessage(TransXChangeConsumer::TYPES['DvsaRecord'], 'ExampleDvsaRecord.xml'),
            $this->createQueueMessage(TransXChangeConsumer::TYPES['RouteMap'], 'ExampleRouteMap.xml', 'auto'),
        );

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->andReturn($messages);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('deleteMessage')
            ->times(20 /* Two batches of 10 messages*/);

        $busRegistration = m::mock(BusRegEntity::class);
        $busRegistration->shouldReceive('getId')->andReturn(1);

        $busRegistration->shouldReceive('isEbsrRefresh')->andReturn(true);
        $busRegistration->shouldReceive('getRegNo')->andReturn(1);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')->andReturn(1);
        $busRegistration->shouldReceive('getLicence')->andReturn($licence);

        $createdBy = m::mock(UserEntity::class);
        $createdBy->shouldReceive('getId')->andReturn(1);
        $busRegistration->shouldReceive('getCreatedBy')->andReturn($createdBy);

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('getBusReg')->andReturn($busRegistration);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchById')
            ->andReturn($ebsrSubmission);

        $this->expectedSideEffect(
            Upload::class,
            [],
            new Result(),
            20 /* All types of requests are uploaded. Each request has 1 document. */
        );

        $this->expectedSideEffect(
            CreateTaskCmd::class,
            [],
            new Result(),
            20 /* All types of requests get a task created. */
        );

        $this->expectedSideEffect(
            UpdateTxcInboxPdfCmd::class,
            [],
            new Result(),
            4 /* Only non-timetable requests are sent to the TxC inbox. */
        );

        $command = TransXChangeConsumerCmd::create([]);
        $this->sut->handleCommand($command);
    }

    public function testMessagesProcessedAndDeletedFromQueue(): void
    {
        $this->setupMocks();

        $messages = array_fill(0, 7, $this->createQueueMessage('Timetable', 'ExampleTimetable.xml'));

        // Ensure that all types of requests are ran.
        array_push(
            $messages,
            $this->createQueueMessage(TransXChangeConsumer::TYPES['Timetable'], 'ExampleTimetable.xml'),
            $this->createQueueMessage(TransXChangeConsumer::TYPES['DvsaRecord'], 'ExampleDvsaRecord.xml'),
            $this->createQueueMessage(TransXChangeConsumer::TYPES['RouteMap'], 'ExampleRouteMap.xml', 'auto'),
        );

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->twice()
            ->andReturn($messages);

        // The loop should break when the `fetchMessages` call is empty or below the batch size (10).
        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->once()
            ->andReturn([]);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('deleteMessage')
            ->times(20 /* Two batches of 10 messages*/);

        $busRegistration = m::mock(BusRegEntity::class);
        $busRegistration->shouldReceive('getId')->andReturn(1);

        $busRegistration->shouldReceive('isEbsrRefresh')->andReturn(true);
        $busRegistration->shouldReceive('getRegNo')->andReturn(1);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')->andReturn(1);
        $busRegistration->shouldReceive('getLicence')->andReturn($licence);

        $createdBy = m::mock(UserEntity::class);
        $createdBy->shouldReceive('getId')->andReturn(1);
        $busRegistration->shouldReceive('getCreatedBy')->andReturn($createdBy);

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('getBusReg')->andReturn($busRegistration);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchById')
            ->andReturn($ebsrSubmission);

        $this->expectedSideEffect(
            Upload::class,
            [],
            new Result(),
            20 /* All types of requests are uploaded. Each request has 1 document. */
        );

        $this->expectedSideEffect(
            CreateTaskCmd::class,
            [],
            new Result(),
            20 /* All types of requests get a task created. */
        );

        $this->expectedSideEffect(
            UpdateTxcInboxPdfCmd::class,
            [],
            new Result(),
            4 /* Only non-timetable requests are sent to the TxC inbox. */
        );

        $command = TransXChangeConsumerCmd::create([]);
        $this->sut->handleCommand($command);
    }

    public function testMessageVisibilityResetIfNoMatchedEbsrSubmission(): void
    {
        $this->setupMocks();

        $messages = [$this->createQueueMessage('Timetable', 'ExampleTimetable.xml')];

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->once()
            ->andReturn($messages);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('changeMessageVisibility')
            ->once();

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchById')
            ->andThrow(NotFoundException::class);

        $command = TransXChangeConsumerCmd::create([]);
        $this->sut->handleCommand($command);
    }

    public function testMessageVisibilityResetIfNoMatchedBusRegistration(): void
    {
        $this->setupMocks();

        $messages = [$this->createQueueMessage('Timetable', 'ExampleTimetable.xml')];

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->once()
            ->andReturn($messages);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('changeMessageVisibility')
            ->once();

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('getBusReg')->andReturn(null);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchById')
            ->andReturn($ebsrSubmission);

        $command = TransXChangeConsumerCmd::create([]);
        $this->sut->handleCommand($command);
    }

    public function testMessageVisibilityResetIfRuntimeException(): void
    {
        $this->setupMocks();

        $messages = [$this->createQueueMessage('Timetable', 'ExampleTimetable.xml')];

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->once()
            ->andReturn($messages);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('changeMessageVisibility')
            ->once();

        $this->repoMap['EbsrSubmission']->shouldReceive('fetchById')->andThrow(RuntimeException::class);

        $command = TransXChangeConsumerCmd::create([]);

        // Exception will be caught, logged, and visibility reset, then rethrown.
        $this->expectException(Exception::class);

        $this->sut->handleCommand($command);
    }

    public function testErrorReturnedByTranXChange(): void
    {
        $this->setupMocks();

        $this->xmlFilter->shouldReceive('filter')->andReturn([
            'error' => 'Something went wrong.'
        ]);

        $messages = [$this->createQueueMessage('Timetable', 'ExampleTimetable.xml')];

        $busRegistration = m::mock(BusRegEntity::class);
        $busRegistration->shouldReceive('getId')->andReturn(1);

        $busRegistration->shouldReceive('isEbsrRefresh')->andReturn(true);
        $busRegistration->shouldReceive('getRegNo')->andReturn(1);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')->andReturn(1);
        $busRegistration->shouldReceive('getLicence')->andReturn($licence);

        $createdBy = m::mock(UserEntity::class);
        $createdBy->shouldReceive('getId')->andReturn(1);
        $busRegistration->shouldReceive('getCreatedBy')->andReturn($createdBy);

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('getBusReg')->andReturn($busRegistration);

        $this->repoMap['EbsrSubmission']
            ->shouldReceive('fetchById')
            ->andReturn($ebsrSubmission);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('fetchMessages')
            ->once()
            ->andReturn($messages);

        $this->mockedSmServices[Queue::class]
            ->shouldReceive('deleteMessage');

        $this->expectedSideEffect(
            CreateTaskCmd::class,
            [],
            new Result()
        );

        $command = TransXChangeConsumerCmd::create([]);

        $this->sut->handleCommand($command);
    }

    protected function createQueueMessage(string $type, string $inputDocumentName, ?string $scale = null): array
    {
        $body = "<xml></xml>";

        $attributes = [
            'Type' => ['StringValue' => $type],
            'InputDocumentName' => ['StringValue' => $inputDocumentName],
        ];

        if (!is_null($scale)) {
            $attributes['Scale'] = ['StringValue' => $scale];
        }

        return [
            'Body' => json_encode($body),
            'MessageAttributes' => $attributes,
            'ReceiptHandle' => '1234',
        ];
    }

    protected function setupMocks()
    {
        // Parent mocks.
        $this->mockedSmServices = [
            Queue::class => m::mock(Queue::class),
            MessageBuilder::class => m::mock(MessageBuilder::class),
            'Config' => $this->config
        ];

        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->queryHandler = m::mock(QueryHandlerManager::class);
        $this->commandHandler = m::mock(CommandHandlerManager::class);
        $this->pidIdentityProvider = m::mock(IdentityProviderInterface::class);
        $this->mockTransationMngr = m::mock(TransactionManagerInterface::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('TransactionManager')->andReturn($this->mockTransationMngr);
        $sm->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandler);
        $sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn($this->commandHandler);
        $sm->shouldReceive('get')->with(IdentityProviderInterface::class)->andReturn($this->pidIdentityProvider);

        // Actual service mocks.
        $this->xmlParser = m::mock(ParseXmlString::class);
        $this->xmlFilter = m::mock(MapXmlFile::class);
        $this->xmlFilter->shouldReceive('setMapping')->andReturnSelf();

        $this->xmlParser->shouldReceive('filter')->andReturn([]);
        $this->xmlFilter->shouldReceive('filter')->byDefault()->andReturn([
            'files' => ['ExampleTimetable.xml']
        ]);

        $filterPluginManager = m::mock(FilterPluginManager::class);
        $filterPluginManager->shouldReceive('get')->with(ParseXmlString::class)->andReturn($this->xmlParser);
        $filterPluginManager->shouldReceive('get')->with(MapXmlFile::class)->andReturn($this->xmlFilter);

        $sm->shouldReceive('get')->with('FilterManager')->andReturn($filterPluginManager);

        $this->xmlValidator = m::mock(Xsd::class);
        $this->xmlValidator->shouldReceive('setXsd')->andReturnSelf();
        $this->xmlValidator->shouldReceive('isValid')->andReturn(true);

        $transXChangeXmlMapping = m::mock(SpecificationInterface::class);

        $validatorManager = m::mock(ValidatorPluginManager::class);
        $validatorManager->shouldReceive('get')->with(Xsd::class)->andReturn($this->xmlValidator);

        $sm->shouldReceive('get')->with('ValidatorManager')->andReturn($validatorManager);
        $sm->shouldReceive('get')->with('TransExchangePublisherXmlMapping')->andReturn($transXChangeXmlMapping);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }

        // AWS Services.
        $stsAssumeRoleResult = new \Aws\Result();
        $stsAssumeRoleResult['Credentials'] = [
            'AccessKeyId' => 'access_key_id',
            'SecretAccessKey' => 'secret_access_key',
            'SessionToken' => 'session_token',
        ];

        m::mock('overload:\Aws\Sts\StsClient')->shouldReceive('AssumeRole')->andReturn($stsAssumeRoleResult);
        m::mock('overload:\Aws\Sqs\SqsClient');
        $this->s3Client = m::mock('overload:\Aws\S3\S3Client');
        $this->s3Client->shouldReceive('getObject')->andReturn(new \Aws\Result(['Body' => '']));

        $this->sut->__invoke($sm, TransXChangeConsumer::class);
    }
}
