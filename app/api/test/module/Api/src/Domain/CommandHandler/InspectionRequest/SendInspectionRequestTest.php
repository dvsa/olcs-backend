<?php

/**
 * Inspection Request / SendInspectionRequest
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\InspectionRequest;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\InspectionRequest as InspectionRequestRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\InspectionRequest\SendInspectionRequest as Cmd;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Service\Client;
use Dvsa\Olcs\Api\Domain\CommandHandler\InspectionRequest\SendInspectionRequest;

/**
 * Inspection Request / SendInspectionRequest
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SendInspectionRequestTest extends CommandHandlerTestCase
{
    private $stubLicenceData = [
        'id' => 77,
        'licNo' => 'OB1234567',
        'licenceType' => [
            'id' => 'ltyp_sn'
        ],
        'totAuthVehicles' => 5,
        'totAuthTrailers' => 6,
        'safetyInsVehicles' => 7,
        'safetyInsTrailers' => 14,
        'operatingCentres' => [
            ['id' => 1],
            ['id' => 2],
        ],
        'expiryDate' => '2020-12-31T12:34:56+00:00',
        'organisation' => [
            'id' => 1,
            'name' => 'Big Old Trucks Ltd.',
            'tradingNames' => [
                ['name' => 'Big Ol\' Wagons'],
                ['name' => 'Keep On Trucking'],
            ],
            'licences' => [
                [
                    'id' => 77,
                    'licNo' => 'OB1234567',
                ],
                [
                    'id' => 78,
                    'licNo' => 'OB1234568',
                ],
                [
                    'id' => 79,
                    'licNo' => 'OB1234569',
                ],
            ],
            'organisationPersons' => [
                [
                    'id' => 3,
                    'person' => [
                        'forename' => 'Mike',
                        'familyName' => 'Smash',
                    ],
                ],
                [
                    'id' => 4,
                    'person' => [
                        'forename' => 'Dave',
                        'familyName' => 'Nice',
                    ],
                ],
            ]
        ],
        'correspondenceCd' => [
            'emailAddress' => 'bigoldtrucks@example.com',
            'address' => [
                'addressLine1' => 'Big Old House',
                'town' => 'Leeds',
                'postcode' => 'LS1 3AD',
            ],
            'phoneContacts' => [
                [
                    'phoneNumber' => '0113 2345678',
                    'phoneContactType' => [
                        'description' => 'Business',
                    ],
                ],
                [
                    'phoneNumber' => '07878 123456',
                    'phoneContactType' => [
                        'description' => 'Mobile',
                    ],
                ],
            ],
        ],
        'tmLicences' => [
            [
                'transportManager' => [
                    'homeCd' => [
                        'person' => [
                            'forename' => 'Bob',
                            'familyName' => 'Smith',
                        ],
                    ],
                ],
            ],
        ],
        'enforcementArea' => [
            'emailAddress' => 'foo@bar.com'
        ],
        'workshops' => [
            [
                'isExternal' => 'Y',
                'maintenance' => 'N',
                'safetyInspection' => 'N',
                'createdOn' => '2015-03-27T12:31:05+0000',
                'id' => 2,
                'contactDetails' => [
                    'address' => [
                        'addressLine1' => 'Inspector Gadget House',
                        'town' => 'Doncaster',
                        'postcode' => 'DN1 1QZ',
                    ],
                ],
            ],
        ],
        'translateToWelsh' => 'N'
    ];

    private $stubApplicationData = [
        'licenceType' => [
            'id' => 'ltyp_si'
        ],
        'totAuthVehicles' => 7,
        'totAuthTrailers' => 8,
        'operatingCentres' => [
            [
                'action' => 'A',
                'operatingCentre' => [
                    'address' => [
                        'addressLine1' => 'Centre One',
                        'town' => 'Leeds',
                    ]
                ],
                'noOfVehiclesRequired' => 2,
                'noOfTrailersRequired' => 4,
            ],
            [
                'action' => 'U',
                'operatingCentre' => [
                    'address' => [
                        'addressLine1' => 'Centre Two',
                        'town' => 'Bradford',
                    ]
                ],
                'noOfVehiclesRequired' => 3,
                'noOfTrailersRequired' => 2,
            ],
            [
                'action' => 'D',
                'operatingCentre' => [
                    'address' => [
                        'addressLine1' => 'Centre Three',
                        'town' => 'Wakefield',
                    ]
                ],
                'noOfVehiclesRequired' => 4,
                'noOfTrailersRequired' => 5,
            ],
        ],
        'licence' => [
            'translateToWelsh' => 'N'
        ]
    ];

    public function setUp()
    {
        $this->sut = new SendInspectionRequest();
        $this->mockRepo('InspectionRequest', InspectionRequestRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
            Client::class => m::mock(Client::class),
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function mockAuthService()
    {
        $mockContactDetails = m::mock(ContactDetails::class)->makePartial();
        $mockContactDetails->setEmailAddress('terry@example.com');

        /** @var Team $mockTeam */
        $mockTeam = m::mock(Team::class)->makePartial();
        $mockTeam->setId(2);

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setTeam($mockTeam);
        $mockUser->setLoginId('terry');
        $mockUser->setContactDetails($mockContactDetails);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);
    }

    public function testHanldeCommandAndPopulateForLicenceRequest()
    {
        $inspectionRequestId = 189781;
        $inspectionRequest = [
            'id' => $inspectionRequestId,
            'requestDate' => '2015-04-17T14:13:56+00:00',
            'dueDate' => '2015-04-18T14:13:56+00:00',
            'licence' => $this->stubLicenceData,
            'application' => null,
            'operatingCentre' => [
                'address' => [
                    'addressLine1' => 'DVSA',
                    'addressLine2' => 'Harehills',
                    'town' => 'Leeds',
                    'postcode' => 'LS9 6NF',
                ],
            ],
            'reportType' => [
                'description' => 'Maintenance Request',
                'refDataCategoryId' => 'insp_report_type',
                'id' => 'insp_rep_t_maint',
            ],
            'requestType' => [
                'description' => 'Change of Entity',
                'refDataCategoryId' => 'insp_request_type',
                'id' => 'insp_req_t_coe',
            ],
            'inspectorNotes' => 'Dolor lorem ipsum',
            'requestorNotes' => 'Lorem ipsum dolor',
            'operatingCentre' =>[
                'id' => 74,
                'address' => [
                    'addressLine1' => 'DVSA',
                    'addressLine2' => 'Harehills',
                    'town' => 'Leeds',
                    'postcode' => 'LS9 6NF',
                ],
            ],
        ];

        $data = [
            'id' => $inspectionRequestId
        ];
        $this->mockAuthService();

        $command = Cmd::create($data);

        $this->repoMap['InspectionRequest']
            ->shouldReceive('fetchForInspectionRequest')
            ->with($inspectionRequestId)
            ->andReturn($inspectionRequest)
            ->once()
            ->getMock();

        $expected = [
            'inspectionRequestId' => $inspectionRequestId,
            'currentUserName' => 'terry',
            'currentUserEmail' => 'terry@example.com',
            'inspectionRequestDateRequested' => '17/04/2015 14:13:56',
            'inspectionRequestNotes' => 'Lorem ipsum dolor',
            'inspectionRequestDueDate' => '18/04/2015 14:13:56',
            'ocAddress' => [
                'addressLine1' => 'DVSA',
                'addressLine2' => 'Harehills',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
            ],
            'inspectionRequestType' => 'Change of Entity',
            'licenceNumber' => 'OB1234567',
            'licenceType' => 'Standard National',
            'totAuthVehicles' => 5,
            'totAuthTrailers' => 6,
            'numberOfOperatingCentres' => 2,
            'expiryDate' => '31/12/2020',
            'operatorId' => 1,
            'operatorName' => 'Big Old Trucks Ltd.',
            'operatorEmail' => 'bigoldtrucks@example.com',
            'operatorAddress' => [
                'addressLine1' => 'Big Old House',
                'town' => 'Leeds',
                'postcode' => 'LS1 3AD',
            ],
            'contactPhoneNumbers' => [
                0 => [
                    'phoneNumber' => '0113 2345678',
                    'phoneContactType' => [
                        'description' => 'Business',
                    ],
                ],
                1 => [
                    'phoneNumber' => '07878 123456',
                    'phoneContactType' => [
                        'description' => 'Mobile',
                    ],
                ]
            ],
            'tradingNames' => [
                'Big Ol\' Wagons',
                'Keep On Trucking',
            ],
            'transportManagers' => [
                'Bob Smith',
            ],
            'workshopIsExternal' => true,
            'safetyInspectionVehicles' => 7,
            'safetyInspectionTrailers' => 14,
            'inspectionProvider' => [
                'address' => [
                    'addressLine1' => 'Inspector Gadget House',
                    'town' => 'Doncaster',
                    'postcode' => 'DN1 1QZ',
                ],
            ],
            'people' => [
                0 => [
                    'forename' => 'Mike',
                    'familyName' => 'Smash',
                ],
                1 => [
                    'forename' => 'Dave',
                    'familyName' => 'Nice',
                ],
            ],
            'otherLicences' => [
                'OB1234568',
                'OB1234569',
            ],
            'applicationOperatingCentres' => [],
        ];

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            'inspection-request',
            $expected,
            null
        );

        $this->mockedSmServices[Client::class]->shouldReceive('sendEmail')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message) use ($inspectionRequestId) {
                $this->assertSame('foo@bar.com', $message->getTo());
                $this->assertSame(
                    "[ Maintenance Inspection ] REQUEST={$inspectionRequestId},STATUS=",
                    $message->getSubject()
                );
                $this->assertSame('en_GB', $message->getLocale());
            }
        );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'messages' => [
                'Inspection request email sent'
            ],
            'id' => []
        ];
        $this->assertEquals($expectedResult, $result->toArray());
    }

    public function testHanldeCommandAndPopulateForApplicationRequest()
    {
        $inspectionRequestId = 189781;
        $inspectionRequest = [
            'id' => $inspectionRequestId,
            'requestDate' => '2015-04-17T14:13:56+00:00',
            'dueDate' => '2015-04-18T14:13:56+00:00',
            'licence' => $this->stubLicenceData,
            'application' => $this->stubApplicationData,
            'operatingCentre' => [
                'address' => [
                    'addressLine1' => 'DVSA',
                    'addressLine2' => 'Harehills',
                    'town' => 'Leeds',
                    'postcode' => 'LS9 6NF',
                ],
            ],
            'reportType' => [
                'description' => 'Maintenance Request',
                'refDataCategoryId' => 'insp_report_type',
                'id' => 'insp_rep_t_maint',
            ],
            'requestType' => [
                'description' => 'Change of Entity',
                'refDataCategoryId' => 'insp_request_type',
                'id' => 'insp_req_t_coe',
            ],
            'inspectorNotes' => 'Dolor lorem ipsum',
            'requestorNotes' => 'Lorem ipsum dolor',
            'operatingCentre' =>[
                'id' => 74,
                'address' => [
                    'addressLine1' => 'DVSA',
                    'addressLine2' => 'Harehills',
                    'town' => 'Leeds',
                    'postcode' => 'LS9 6NF',
                ],
            ],
        ];

        $data = [
            'id' => $inspectionRequestId
        ];
        $this->mockAuthService();

        $command = Cmd::create($data);

        $this->repoMap['InspectionRequest']
            ->shouldReceive('fetchForInspectionRequest')
            ->with($inspectionRequestId)
            ->andReturn($inspectionRequest)
            ->once()
            ->getMock();

        $expected = [
            'inspectionRequestId' => $inspectionRequestId,
            'currentUserName' => 'terry',
            'currentUserEmail' => 'terry@example.com',
            'inspectionRequestDateRequested' => '17/04/2015 14:13:56',
            'inspectionRequestNotes' => 'Lorem ipsum dolor',
            'inspectionRequestDueDate' => '18/04/2015 14:13:56',
            'ocAddress' => [
                'addressLine1' => 'DVSA',
                'addressLine2' => 'Harehills',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
            ],
            'inspectionRequestType' => 'Change of Entity',
            'licenceNumber' => 'OB1234567',
            'licenceType' => 'Standard International',
            'totAuthVehicles' => 7,
            'totAuthTrailers' => 8,
            'numberOfOperatingCentres' => 2,
            'expiryDate' => '31/12/2020',
            'operatorId' => 1,
            'operatorName' => 'Big Old Trucks Ltd.',
            'operatorEmail' => 'bigoldtrucks@example.com',
            'operatorAddress' => [
                'addressLine1' => 'Big Old House',
                'town' => 'Leeds',
                'postcode' => 'LS1 3AD',
            ],
            'contactPhoneNumbers' => [
                0 => [
                    'phoneNumber' => '0113 2345678',
                    'phoneContactType' => [
                        'description' => 'Business',
                    ],
                ],
                1 => [
                    'phoneNumber' => '07878 123456',
                    'phoneContactType' => [
                        'description' => 'Mobile',
                    ],
                ]
            ],
            'tradingNames' => [
                'Big Ol\' Wagons',
                'Keep On Trucking',
            ],
            'transportManagers' => [
                'Bob Smith',
            ],
            'workshopIsExternal' => true,
            'safetyInspectionVehicles' => 7,
            'safetyInspectionTrailers' => 14,
            'inspectionProvider' => [
                'address' => [
                    'addressLine1' => 'Inspector Gadget House',
                    'town' => 'Doncaster',
                    'postcode' => 'DN1 1QZ',
                ],
            ],
            'people' => [
                0 => [
                    'forename' => 'Mike',
                    'familyName' => 'Smash',
                ],
                1 => [
                    'forename' => 'Dave',
                    'familyName' => 'Nice',
                ],
            ],
            'otherLicences' => [
                'OB1234568',
                'OB1234569',
            ],
            'applicationOperatingCentres' => [
                0 => [
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Centre One',
                            'town' => 'Leeds',
                        ],
                    ],
                    'noOfVehiclesRequired' => 2,
                    'noOfTrailersRequired' => 4,
                    'action' => 'Added',
                ],
                1 => [
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Centre Two',
                            'town' => 'Bradford',
                        ],
                    ],
                    'noOfVehiclesRequired' => 3,
                    'noOfTrailersRequired' => 2,
                    'action' => 'Updated',
                ],
                2 => [
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Centre Three',
                            'town' => 'Wakefield',
                        ],
                    ],
                    'noOfVehiclesRequired' => 4,
                    'noOfTrailersRequired' => 5,
                    'action' => 'Deleted',
                ],
            ],
        ];

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->with(
            m::type(\Dvsa\Olcs\Email\Data\Message::class),
            'inspection-request',
            $expected,
            null
        );

        $this->mockedSmServices[Client::class]->shouldReceive('sendEmail')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message) use ($inspectionRequestId) {
                $this->assertSame('foo@bar.com', $message->getTo());
                $this->assertSame(
                    "[ Maintenance Inspection ] REQUEST={$inspectionRequestId},STATUS=",
                    $message->getSubject()
                );
                $this->assertSame('en_GB', $message->getLocale());
            }
        );

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            'messages' => [
                'Inspection request email sent'
            ],
            'id' => []
        ];
        $this->assertEquals($expectedResult, $result->toArray());
    }
}
