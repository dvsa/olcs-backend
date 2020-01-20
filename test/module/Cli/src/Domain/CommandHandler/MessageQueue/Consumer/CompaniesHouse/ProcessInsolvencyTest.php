<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Command\Email\SendLiquidatedCompanyForRegisteredUser;
use Dvsa\Olcs\Api\Domain\Command\Email\SendLiquidatedCompanyForUnregisteredUser;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseCompany;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Team;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany as CHCompanyEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvency as ProcessInsolvencyCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\AbstractConsumer;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvency;
use Dvsa\Olcs\CompaniesHouse\Service\Client as CompaniesHouseClient;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Dvsa\Olcs\Queue\Service\Queue;
use Mockery as m;

class ProcessInsolvencyTest extends CompaniesHouseConsumerTestCase
{
    protected $config = [
        'message_queue' => [
            'ProcessInsolvency_URL' => 'process_insolvency_queue_url'
        ]
    ];

    public function setUp()
    {
        $this->sut = new ProcessInsolvency();
        $this->mockRepo('CompaniesHouseCompany', CompaniesHouseCompany::class);
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('Team', Team::class);
    }

    public function testHandleCommand()
    {
        $this->setupStandardService();

        $mockCompany = m::mock(CHCompanyEntity::class);
        $mockCompany->shouldReceive('getCompanyNumber')
            ->andReturn('1234');
        $mockCompany->shouldReceive('setInsolvencyProcessed')
            ->with(true);

        $mockCompany->shouldReceive('setInsolvencyPractitioners')
            ->once();

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('getLatestByCompanyNumber')
            ->once()
            ->andReturn($mockCompany);

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('save')
            ->with($mockCompany)
            ->twice();


        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->with('1234')
            ->andReturn(
                [
                    m::mock(Organisation::class)
                        ->shouldReceive('getActiveLicences')
                        ->andReturn(new ArrayCollection([]))
                        ->getMock()
                ]
            )->getMock();

        $command = ProcessInsolvencyCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            '2 insolvency practitioners added for company 1234'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    /**
     * @dataProvider addressData
     */
    public function testHandleCommandCreatesTasks($licence, $team)
    {
        $this->setupStandardService();

        $mockCompany = m::mock(CHCompanyEntity::class);
        $mockCompany->shouldReceive('getCompanyNumber')
            ->andReturn('1234');
        $mockCompany->shouldReceive('getCompanyStatus')
            ->andReturn('TEST');
        $mockCompany->shouldReceive('setInsolvencyProcessed')
            ->with(true);

        $mockCompany->shouldReceive('setInsolvencyPractitioners')
            ->once();

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('getLatestByCompanyNumber')
            ->once()
            ->andReturn($mockCompany);

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('save')
            ->with($mockCompany)
            ->twice();

        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->with('1234')
            ->andReturn(
                [
                    m::mock(Organisation::class)
                        ->shouldReceive('getActiveLicences')
                        ->andReturn(new ArrayCollection([$licence]))
                        ->getMock()
                ]
            )->getMock();

        $this->repoMap['Team']
            ->shouldReceive('fetchOneByName')
            ->with($team)
            ->andReturn(
                m::mock(TeamEntity::class)
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->getMock()
            );

        $this->expectedSideEffect(GenerateAndStoreWithMultipleAddresses::class, [], new Result());
        $this->expectedSideEffect(Create::class, [], new Result(), 4);

        $command = ProcessInsolvencyCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            '2 insolvency practitioners added for company 1234'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    /**
     * @dataProvider emailTestsDataProvider
     */
    public function testHandleCommandSendsEmails($licence)
    {
        $this->setupStandardService();

        $mockCompany = m::mock(CHCompanyEntity::class);
        $mockCompany->shouldReceive('getCompanyNumber')
            ->andReturn('1234');
        $mockCompany->shouldReceive('getCompanyStatus')
            ->andReturn('TEST');
        $mockCompany->shouldReceive('setInsolvencyProcessed')
            ->with(true);

        $mockCompany->shouldReceive('setInsolvencyPractitioners')
            ->once();

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('getLatestByCompanyNumber')
            ->once()
            ->andReturn($mockCompany);

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('save')
            ->with($mockCompany)
            ->twice();

        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->with('1234')
            ->andReturn(
                [
                    m::mock(Organisation::class)
                        ->shouldReceive('getActiveLicences')
                        ->andReturn(new ArrayCollection([$licence]))
                        ->getMock()
                ]
            )->getMock();

        $this->repoMap['Team']
            ->shouldReceive('fetchOneByName')
            ->andReturn(
                m::mock(TeamEntity::class)
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->getMock()
            );

        $this->expectedSideEffect(GenerateAndStoreWithMultipleAddresses::class, [], new Result());
        $this->expectedSideEffect(Create::class, [], new Result(), 4);

        $command = ProcessInsolvencyCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            '2 insolvency practitioners added for company 1234'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    public function testHandleCommandSendsEmailsWithNoRegisteredUsers()
    {
        $this->setupStandardService();

        $licence = $this->getMockLicences()[2];

        $mockCompany = m::mock(CHCompanyEntity::class);
        $mockCompany->shouldReceive('getCompanyNumber')
            ->andReturn('1234');
        $mockCompany->shouldReceive('getCompanyStatus')
            ->andReturn('TEST');
        $mockCompany->shouldReceive('setInsolvencyProcessed')
            ->with(true);

        $mockCompany->shouldReceive('setInsolvencyPractitioners')
            ->once();

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('getLatestByCompanyNumber')
            ->once()
            ->andReturn($mockCompany);

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('save')
            ->with($mockCompany)
            ->twice();

        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->with('1234')
            ->andReturn(
                [
                    m::mock(Organisation::class)
                        ->shouldReceive('getActiveLicences')
                        ->andReturn(new ArrayCollection([$licence]))
                        ->getMock()
                ]
            )->getMock();

        $this->repoMap['Team']
            ->shouldReceive('fetchOneByName')
            ->andReturn(
                m::mock(TeamEntity::class)
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->getMock()
            );

        $this->expectedSideEffect(
            GenerateAndStoreWithMultipleAddresses::class,
            [],
            new Result()
        );
        $this->expectedSideEffect(
            Create::class,
            [],
            new Result(),
            2
        );

        $command = ProcessInsolvencyCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            '2 insolvency practitioners added for company 1234'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    public function testHandleCommandSendsEmailsWithNoCorrespondenceEmail()
    {
        $this->setupStandardService();

        $licence = $this->getMockLicences()[3];

        $mockCompany = m::mock(CHCompanyEntity::class);
        $mockCompany->shouldReceive('getCompanyNumber')
            ->andReturn('1234');
        $mockCompany->shouldReceive('getCompanyStatus')
            ->andReturn('TEST');
        $mockCompany->shouldReceive('setInsolvencyProcessed')
            ->with(true);

        $mockCompany->shouldReceive('setInsolvencyPractitioners')
            ->once();

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('getLatestByCompanyNumber')
            ->once()
            ->andReturn($mockCompany);

        $this->repoMap['CompaniesHouseCompany']
            ->shouldReceive('save')
            ->with($mockCompany)
            ->twice();

        $this->repoMap['Organisation']
            ->shouldReceive('getByCompanyOrLlpNo')
            ->with('1234')
            ->andReturn(
                [
                    m::mock(Organisation::class)
                        ->shouldReceive('getActiveLicences')
                        ->andReturn(new ArrayCollection([$licence]))
                        ->getMock()
                ]
            )->getMock();

        $this->repoMap['Team']
            ->shouldReceive('fetchOneByName')
            ->andReturn(
                m::mock(TeamEntity::class)
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->getMock()
            );

        $this->expectedSideEffect(
            GenerateAndStoreWithMultipleAddresses::class,
            [],
            new Result()
        );
        $this->expectedSideEffect(
            Create::class,
            [],
            new Result()
        );

        $command = ProcessInsolvencyCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            '2 insolvency practitioners added for company 1234',
            'Unable to send emails: No email addresses found'
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    public function testHandleCommandNoMessages()
    {
        $queueService = m::mock(Queue::class);

        $queueService->shouldReceive('fetchMessages')
            ->with('process_insolvency_queue_url', 1)
            ->andReturnNull()
            ->once();

        $this->mockedSmServices = [
            CompaniesHouseClient::class => m::mock(CompaniesHouseClient::class),
            Queue::class => $queueService,
            MessageBuilder::class => m::mock(MessageBuilder::class),
            'Config' => $this->config
        ];

        $this->setupService();

        $command = ProcessInsolvencyCmd::create([]);
        $response = $this->sut->handleCommand($command);

        $messages = [
            AbstractConsumer::NOTHING_TO_PROCESS_MESSAGE
        ];

        $this->assertEquals($messages, $response->getMessages());
    }

    protected function getMockQueueService()
    {
        $queueService = m::mock(Queue::class);

        $queueService->shouldReceive('fetchMessages')
            ->with('process_insolvency_queue_url', 1)
            ->andReturn([
                [
                    'Body' => '1234',
                    'ReceiptHandle' => 1
                ]
            ])
            ->once();

        $queueService->shouldReceive('deleteMessage')
            ->with('process_insolvency_queue_url', 1)
            ->once();

        return $queueService;
    }

    protected function getMockCompaniesHouseClient()
    {
        $mockClient = m::mock(CompaniesHouseClient::class);
        $mockClient->shouldReceive('getInsolvencyDetails')
            ->once()
            ->andReturn($this->mockInsolvencyDataResponse());

        return $mockClient;
    }

    protected function mockInsolvencyDataResponse()
    {
        return [
            [
                'practitioners' => [
                    0 => [
                        'role' => 'practitioner',
                        'address' => [
                            'postal_code' => 'postal code',
                            'address_line_1' => 'address line 1',
                            'locality' => 'locality',
                            'address_line_2' => 'address line 2',
                        ],
                        'name' => 'Edwin Hubble',
                    ],
                    1 => [
                        'address' => [
                            'locality' => 'locality',
                            'address_line_1' => 'address line 1',
                            'postal_code' => 'postal code',
                            'address_line_2' => 'address line 2',
                        ],
                        'name' => 'Alice Cooper',
                        'role' => 'practitioner',
                    ],
                ],
                'number' => '1',
                'type' => 'in-administration',
                'dates' => [
                    0 => [
                        'type' => 'administration-started-on',
                        'date' => '2019-07-24',
                    ],
                ],
            ],
        ];
    }

    private function getMockLicences()
    {
        $mockOrgGB = $this->getMockOrganisationWithUserEmails([
            'emailgb1@example.com',
            'emailgb2@example.com'
        ]);

        $mockOrgNI = $this->getMockOrganisationWithUserEmails([
            'emailni1@example.com',
            'emailni2@example.com'
        ]);

        $mockOrgGBNoRegisteredUsers = $this->getMockOrganisationWithUserEmails([]);

        $mockCorrespondenceDetailsGB = m::mock(ContactDetails::class)
            ->shouldReceive('getEmailAddress')
            ->andReturn('gbcorrespondenceemail@example.com')
            ->getMock();

        $mockCorrespondenceDetailsNI = m::mock(ContactDetails::class)
            ->shouldReceive('getEmailAddress')
            ->andReturn('nicorrespondenceemail@example.com')
            ->getMock();

        $mockCorrespondenceDetailsNoEmail = m::mock(ContactDetails::class)
            ->shouldReceive('getEmailAddress')
            ->andReturnNull()
            ->getMock();

        $mockGBLicence = m::mock(Licence::class)
            ->shouldReceive('isNi')
            ->andReturn(false)
            ->shouldReceive('isRestricted')
            ->andReturn(true)
            ->shouldReceive('isNi')
            ->andReturn(false)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->shouldReceive('isPsv')
            ->andReturn(true)
            ->shouldReceive('isRestricted')
            ->andReturn(false)
            ->shouldReceive('getTranslateToWelsh')
            ->andReturn('Y')
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($mockCorrespondenceDetailsGB)
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrgGB)
            ->getMock();

        $mockNiLicence = m::mock(Licence::class)
            ->shouldReceive('isNi')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(true)
            ->shouldReceive('isNi')
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('getId')
            ->andReturn(2)
            ->shouldReceive('isRestricted')
            ->andReturn(false)
            ->shouldReceive('getTranslateToWelsh')
            ->andReturn('N')
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($mockCorrespondenceDetailsNI)
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrgNI)
            ->getMock();

        $mockGBLicenceNoRegisteredUsers = m::mock(Licence::class)
            ->shouldReceive('isNi')
            ->andReturn(true)
            ->shouldReceive('isRestricted')
            ->andReturn(true)
            ->shouldReceive('isNi')
            ->andReturn(false)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->shouldReceive('isPsv')
            ->andReturn(true)
            ->shouldReceive('isRestricted')
            ->andReturn(false)
            ->shouldReceive('getTranslateToWelsh')
            ->andReturn('N')
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($mockCorrespondenceDetailsGB)
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrgGBNoRegisteredUsers)
            ->getMock();


        $mockGBLicenceNoCorrespondenceEmail = m::mock(Licence::class)
            ->shouldReceive('isNi')
            ->andReturn(true)
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('isNi')
            ->andReturn(false)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->shouldReceive('isPsv')
            ->andReturn(true)
            ->shouldReceive('isRestricted')
            ->andReturn(false)
            ->shouldReceive('getTranslateToWelsh')
            ->andReturn('N')
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($mockCorrespondenceDetailsNoEmail)
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrgGBNoRegisteredUsers)
            ->getMock();

        return new ArrayCollection([
            $mockGBLicence,
            $mockNiLicence,
            $mockGBLicenceNoRegisteredUsers,
            $mockGBLicenceNoCorrespondenceEmail
        ]);
    }

    private function getMockOrganisationWithUserEmails(array $emails)
    {
        $mockUsers = array_map(
            static function ($emailAddress) {
                $mockUserCD = m::Mock(ContactDetails::class)
                    ->shouldReceive('getEmailAddress')
                    ->andReturn($emailAddress)
                    ->getMock();

                $mockUser = m::Mock(User::class)
                    ->shouldReceive('getContactDetails')
                    ->andReturn($mockUserCD)
                    ->getMock();

                return m::Mock(OrganisationUser::class)
                    ->shouldReceive('getUser')
                    ->andReturn($mockUser)
                    ->getMock();
            },
            $emails
        );

        return (m::Mock(Organisation::class)
            ->shouldReceive('getAdministratorUsers')
            ->andReturn(new ArrayCollection($mockUsers))
            ->getMock());
    }

    public function addressData()
    {
        return [
            'GBLicence' => [
                $this->getMockLicences()[0],
                ProcessInsolvency::GB_TEAMLEADER_TASK,
            ],
            'NILicence' => [
                $this->getMockLicences()[1],
                ProcessInsolvency::NI_TEAMLEADER_TASK,
            ]
        ];
    }

    public function emailTestsDataProvider()
    {
        return [
            'GBLicence' => [
                $this->getMockLicences()[0],
            ],
            'NILicence' => [
                $this->getMockLicences()[1],
            ]
        ];
    }

    protected function setupStandardService()
    {
        $this->mockedSmServices = [
            CompaniesHouseClient::class => $this->getMockCompaniesHouseClient(),
            Queue::class => $this->getMockQueueService(),
            MessageBuilder::class => m::mock(MessageBuilder::class),
            'Config' => $this->config
        ];
        $this->setupService();
    }
}
