<?php

/**
 * Update Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Organisation\UpdateTradingNames;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateBusinessDetails;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateBusinessDetails as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * Update Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateBusinessDetailsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateBusinessDetails();
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('Organisation', Organisation::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            '01110',
            '01120',
            '01130'
        ];

        $this->references = [
            ContactDetails::class => [
                123 => m::mock(ContactDetails::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutPermissionWhenChangingName()
    {
        $data = [
            'id' => 111,
            'name' => 'Changed name ltd'
        ];
        $command = Cmd::create($data);

        /** @var OrganisationEntity $licence */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setName('Original name ltd');
        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(true);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->setExpectedException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithoutPermissionWhenChangingCompanyNo()
    {
        $data = [
            'id' => 111,
            'name' => 'Original name ltd',
            'companyOrLlpNo' => '12345678'
        ];
        $command = Cmd::create($data);

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setName('Original name ltd');
        $organisation->setCompanyOrLlpNo('87654321');
        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(true);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->setExpectedException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithPermission()
    {
        $data = [
            'id' => 111,
            'version' => 2,
            'name' => 'Changed name ltd',
            'companyOrLlpNo' => '12345678',
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ],
            'registeredAddress' => [
                'addressLine1' => 'Address 1',
                'postcode' => 'AB1 1AB'
            ],
            'natureOfBusiness' => 'Stuff',
        ];
        $command = Cmd::create($data);

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(111);
        $organisation->setName('Original name ltd');
        $organisation->setCompanyOrLlpNo('87654321');
        $organisation->setNatureOfBusiness('Old Stuff');

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Organisation']->shouldReceive('lock')
            ->with($organisation, 2)
            ->shouldReceive('save')
            ->with($organisation);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        // Update trading names
        $expectedData = [
            'licence' => 222,
            'organisation' => null,
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ]
        ];
        $result1 = new Result();
        $result1->setFlag('hasChanged', true);
        $result1->addMessage('Trading names updated');
        $this->expectedSideEffect(UpdateTradingNames::class, $expectedData, $result1);

        // Save registered address
        $expectedData = [
            'addressLine1' => 'Address 1',
            'postcode' => 'AB1 1AB',
            'contactType' => ContactDetails::CONTACT_TYPE_REGISTERED_ADDRESS,
            'id' => null,
            'version' => null,
            'addressLine2' => null,
            'addressLine3' => null,
            'addressLine4' => null,
            'town' => null,
            'countryCode' => null,
        ];
        $result2 = new Result();
        $result2->setFlag('hasChanged', false);
        $result2->addMessage('Address created');
        $result2->addId('contactDetails', 123);
        $this->expectedSideEffect(SaveAddress::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'contactDetails' => 123
            ],
            'messages' => [
                'Trading names updated',
                'Address created',
                'Organisation updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('Changed name ltd', $organisation->getName());
        $this->assertEquals('12345678', $organisation->getCompanyOrLlpNo());
    }

    public function testHandleCommandWithPermissionWithoutChange()
    {
        $data = [
            'id' => 111,
            'version' => 2,
            'name' => 'Original name ltd',
            'companyOrLlpNo' => '12345678',
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ],
            'registeredAddress' => [
                'addressLine1' => 'Address 1',
                'postcode' => 'AB1 1AB'
            ],
            'natureOfBusiness' => 'Stuff',
        ];
        $command = Cmd::create($data);

        $nobCollection = new ArrayCollection();
        $nobCollection->add($this->refData['01110']);

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(111);
        $organisation->setName('Original name ltd');
        $organisation->setCompanyOrLlpNo('12345678');
        $organisation->setNatureOfBusiness('Stuff');
        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(false);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Organisation']->shouldReceive('lock')
            ->with($organisation, 2)
            ->shouldReceive('save')
            ->with($organisation);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false)
            ->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        // Update trading names
        $expectedData = [
            'licence' => 222,
            'organisation' => null,
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ]
        ];
        $result1 = new Result();
        $result1->setFlag('hasChanged', false);
        $result1->addMessage('Trading names unchanged');
        $this->expectedSideEffect(UpdateTradingNames::class, $expectedData, $result1);

        // Save registered address
        $expectedData = [
            'addressLine1' => 'Address 1',
            'postcode' => 'AB1 1AB',
            'contactType' => ContactDetails::CONTACT_TYPE_REGISTERED_ADDRESS,
            'id' => null,
            'version' => null,
            'addressLine2' => null,
            'addressLine3' => null,
            'addressLine4' => null,
            'town' => null,
            'countryCode' => null
        ];
        $result2 = new Result();
        $result2->setFlag('hasChanged', false);
        $result2->addMessage('Address unchanged');
        $this->expectedSideEffect(SaveAddress::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Trading names unchanged',
                'Address unchanged',
                'Organisation unchanged'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithPermissionWithChangeSelfserve()
    {
        $data = [
            'id' => 111,
            'version' => 2,
            'name' => 'Original name ltd',
            'companyOrLlpNo' => '87654321',
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ],
            'registeredAddress' => [
                'addressLine1' => 'Address 1',
                'postcode' => 'AB1 1AB'
            ],
            'natureOfBusiness' => 'Stuff',
        ];
        $command = Cmd::create($data);

        $nobCollection = new ArrayCollection();
        $nobCollection->add($this->refData['01110']);

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(111);
        $organisation->setName('Original name ltd');
        $organisation->setCompanyOrLlpNo('12345678');
        $organisation->setNatureOfBusiness('Stuff');
        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(false);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Organisation']->shouldReceive('lock')
            ->with($organisation, 2)
            ->shouldReceive('save')
            ->with($organisation);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false)
            ->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        // Update trading names
        $expectedData = [
            'licence' => 222,
            'organisation' => null,
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ]
        ];
        $result1 = new Result();
        $result1->setFlag('hasChanged', false);
        $result1->addMessage('Trading names unchanged');
        $this->expectedSideEffect(UpdateTradingNames::class, $expectedData, $result1);

        // Save registered address
        $expectedData = [
            'addressLine1' => 'Address 1',
            'postcode' => 'AB1 1AB',
            'contactType' => ContactDetails::CONTACT_TYPE_REGISTERED_ADDRESS,
            'id' => null,
            'version' => null,
            'addressLine2' => null,
            'addressLine3' => null,
            'addressLine4' => null,
            'town' => null,
            'countryCode' => null
        ];
        $result2 = new Result();
        $result2->setFlag('hasChanged', false);
        $result2->addMessage('Address unchanged');
        $this->expectedSideEffect(SaveAddress::class, $expectedData, $result2);

        // Create task
        $expectedData = [
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::TASK_SUB_CATEGORY_BUSINESS_DETAILS_CHANGE,
            'description' => 'Change to business details',
            'licence' => 222,
            'actionDate' => null,
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'isClosed' => false,
            'urgent' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'irfoOrganisation' => null,
        ];
        $result3 = new Result();
        $result3->addId('task', 321);
        $result3->addMessage('Task created');
        $this->expectedSideEffect(CreateTask::class, $expectedData, $result3);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 321
            ],
            'messages' => [
                'Trading names unchanged',
                'Address unchanged',
                'Organisation updated',
                'Task created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutPermissionWithoutChange()
    {
        $data = [
            'id' => 111,
            'version' => 2,
            'name' => 'Original name ltd',
            'companyOrLlpNo' => '12345678',
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ],
            'registeredAddress' => [
                'addressLine1' => 'Address 1',
                'postcode' => 'AB1 1AB'
            ],
            'natureOfBusiness' => 'Stuff',
        ];
        $command = Cmd::create($data);

        $nobCollection = new ArrayCollection();
        $nobCollection->add($this->refData['01110']);

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(111);
        $organisation->setName('Original name ltd');
        $organisation->setCompanyOrLlpNo('12345678');
        $organisation->setNatureOfBusiness('Stuff');
        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(true);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Organisation']->shouldReceive('lock')
            ->with($organisation, 2)
            ->shouldReceive('save')
            ->with($organisation);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false)
            ->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        // Update trading names
        $expectedData = [
            'licence' => 222,
            'organisation' => null,
            'tradingNames' => [
                'Foo ltd',
                'Bar ltd'
            ]
        ];
        $result1 = new Result();
        $result1->setFlag('hasChanged', true);
        $result1->addMessage('Trading names updated');
        $this->expectedSideEffect(UpdateTradingNames::class, $expectedData, $result1);

        // Save registered address
        $expectedData = [
            'addressLine1' => 'Address 1',
            'postcode' => 'AB1 1AB',
            'contactType' => ContactDetails::CONTACT_TYPE_REGISTERED_ADDRESS,
            'id' => null,
            'version' => null,
            'addressLine2' => null,
            'addressLine3' => null,
            'addressLine4' => null,
            'town' => null,
            'countryCode' => null
        ];
        $result2 = new Result();
        $result2->setFlag('hasChanged', false);
        $result2->addMessage('Address unchanged');
        $this->expectedSideEffect(SaveAddress::class, $expectedData, $result2);

        // Create task
        $expectedData = [
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::TASK_SUB_CATEGORY_BUSINESS_DETAILS_CHANGE,
            'description' => 'Change to business details',
            'licence' => 222,
            'actionDate' => null,
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'isClosed' => false,
            'urgent' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'irfoOrganisation' => null,
        ];
        $result3 = new Result();
        $result3->addId('task', 321);
        $result3->addMessage('Task created');
        $this->expectedSideEffect(CreateTask::class, $expectedData, $result3);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 321
            ],
            'messages' => [
                'Trading names updated',
                'Address unchanged',
                'Organisation unchanged',
                'Task created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
