<?php

/**
 * Save Operator Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Operator;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Operator\SaveOperator;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Person as PersonRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Operator\Create as CreateCmd;
use Dvsa\Olcs\Transfer\Command\Operator\Update as UpdateCmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Doctrine\ORM\Query;

/**
 * Save Operator Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SaveOperatorTest extends CommandHandlerTestCase
{
    const NATURE_OF_BUSINESS = 'testnob';

    public function setUp()
    {
        $this->sut = new SaveOperator();
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('OrganisationPerson', OrganisationPersonRepo::class);
        $this->mockRepo('Person', PersonRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            self::NATURE_OF_BUSINESS,
            OrganisationEntity::ORG_TYPE_PARTNERSHIP,
            OrganisationEntity::ORG_TYPE_OTHER,
            OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY,
            OrganisationEntity::ORG_TYPE_LLP,
            OrganisationEntity::ORG_TYPE_SOLE_TRADER,
            OrganisationEntity::ORG_TYPE_IRFO
        ];

        $this->references = [
            ContactDetailsEntity::class => [
                10 => m::mock(ContactDetailsEntity::class)
            ],
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider organisationProvider
     */
    public function testHandleCommandOrganisationNotValid($commandDetails, $expectedErrors)
    {
        $data = [
            'businessType' => $commandDetails['businessType'],
            'name' => $commandDetails['name'],
            'natureOfBusiness' => $commandDetails['natureOfBusiness'],
            'companyNumber' => $commandDetails['companyNumber'],
            'lastName' => $commandDetails['lastName']
        ];

        $this->setExpectedException(ValidationException::class, $expectedErrors);

        $command = CreateCmd::create($data);
        $this->sut->handleCommand($command);
    }

    public function organisationProvider()
    {
        return [
            [
                // ORG_TYPE_PARTNERSHIP or ORG_TYPE_OTHER
                [
                    'businessType' => OrganisationEntity::ORG_TYPE_PARTNERSHIP,
                    'name' => null,
                    'natureOfBusiness' => null,
                    'companyNumber' => '12345678',
                    'lastName' => 'lname'
                ],
                [
                    'name' => ['Operator name is required'],
                    'natureOfBusiness' => ['Nature of Business is required']
                ]
            ],
            [
                // ORG_TYPE_REGISTERED_COMPANY or OrganisationEntity::ORG_TYPE_LLP
                [
                    'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY,
                    'name' => null,
                    'natureOfBusiness' => null,
                    'companyNumber' => null,
                    'lastName' => 'lname'
                ],
                [
                    'name' => ['Operator name is required'],
                    'companyNumber' => ['Company Number is required'],
                    'natureOfBusiness' => ['Nature of Business is required']
                ]
            ],
            [
                // ORG_TYPE_SOLE_TRADER
                [
                    'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER,
                    'name' => 'foo',
                    'natureOfBusiness' => null,
                    'companyNumber' => 'bar',
                    'lastName' => null
                ],
                [
                    'name' => ['Operator name is required'],
                    'natureOfBusiness' => ['Nature of Business is required']
                ]
            ],
            [
                // ORG_TYPE_IRFO
                [
                    'businessType' => OrganisationEntity::ORG_TYPE_IRFO,
                    'name' => null,
                    'natureOfBusiness' => null,
                    'companyNumber' => 'foo',
                    'lastName' => 'bar'
                ],
                [
                    'name' => ['Operator name is required'],
                ]
            ],
            [
                // Unknown type
                [
                    'businessType' => 'foo',
                    'name' => null,
                    'natureOfBusiness' => null,
                    'companyNumber' => null,
                    'lastName' => null
                ],
                [
                    [SaveOperator::ERROR_UNKNOWN_TYPE => 'Unknown business type']
                ]
            ]
        ];
    }

    public function testHandleCommandCreateRcOrLlp()
    {
        $data = [
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY,
            'name' => 'name',
            'natureOfBusiness' => [self::NATURE_OF_BUSINESS],
            'companyNumber' => '12345678',
            'firstName' => null,
            'lastName' => null,
            'isIrfo' => 'Y',
            'address' => [
                'id' => 1,
                'version' => 2,
                'addressLine1' => 'al1',
                'addressLine2' => 'al2',
                'addressLine3' => 'al3',
                'addressLine4' => 'al4',
                'postcode' => 'pc'
            ]
        ];

        $command = CreateCmd::create($data);

        $address = $data['address'];
        $address['countryCode'] = null;
        $address['town'] = null;
        $address['contactType'] = AddressEntity::CONTACT_TYPE_REGISTERED_ADDRESS;
        $addressResult = new Result();
        $addressResult->addId('contactDetails', 1);

        $this->expectedSideEffect(
            SaveAddressCmd::class,
            $address,
            $addressResult
        );

        $savedOrganisation = null;

        $this->repoMap['Organisation']->shouldReceive('save')
            ->with(m::type(OrganisationEntity::class))
            ->andReturnUsing(
                function (OrganisationEntity $organisation) use (&$savedOrganisation) {
                    $organisation->setId(1);
                    $savedOrganisation = $organisation;
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(1, $result->getIds()['organisation']);
        $this->assertEquals('Organisation created successfully', $result->getMessages()[0]);
    }

    public function testHandleCommandUpdatePartnershipOrOther()
    {
        $data = [
            'businessType' => OrganisationEntity::ORG_TYPE_PARTNERSHIP,
            'name' => 'name',
            'natureOfBusiness' => [self::NATURE_OF_BUSINESS],
            'companyNumber' => null,
            'firstName' => null,
            'lastName' => null,
            'isIrfo' => 'Y',
            'address' => null,
            'id' => 1,
            'version' => 2
        ];

        $command = UpdateCmd::create($data);

        $mockOrganisation = m::mock(OrganisationEntity::class)->makePartial();
        $mockOrganisation->setId(1);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockOrganisation)
            ->once()
            ->shouldReceive('save')
            ->with($mockOrganisation)
            ->once();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(1, $result->getIds()['organisation']);
        $this->assertEquals('Organisation updated successfully', $result->getMessages()[0]);

    }

    public function testHandleCommandUpdateSoleTrader()
    {
        $data = [
            'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER,
            'natureOfBusiness' => [self::NATURE_OF_BUSINESS],
            'firstName' => 'fname',
            'lastName' => 'lname',
            'isIrfo' => 'Y',
            'id' => 1,
            'version' => 2,
            'personId' => 3,
            'personVersion' => 4
        ];

        $command = UpdateCmd::create($data);

        $mockOrganisation = m::mock(OrganisationEntity::class)->makePartial();
        $mockOrganisation->setId(1);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockOrganisation)
            ->once()
            ->shouldReceive('save')
            ->with($mockOrganisation)
            ->once();

        $mockPerson = m::mock(PersonEntity::class)->makePartial();
        $mockPerson->setId(3);

        $this->repoMap['Person']->shouldReceive('fetchById')
            ->with(3, Query::HYDRATE_OBJECT, 4)
            ->andReturn($mockPerson)
            ->once()
            ->shouldReceive('save')
            ->with($mockPerson)
            ->once();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(1, $result->getIds()['organisation']);
        $this->assertEquals('Organisation updated successfully', $result->getMessages()[0]);

    }

    public function testHandleCommandCreateSoleTrader()
    {
        $data = [
            'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER,
            'natureOfBusiness' => [self::NATURE_OF_BUSINESS],
            'firstName' => 'fname',
            'lastName' => 'lname',
            'isIrfo' => 'Y',
            'id' => 1,
            'version' => 2,
            'personId' => null,
            'personVersion' => null
        ];

        $command = UpdateCmd::create($data);

        $mockOrganisation = m::mock(OrganisationEntity::class)->makePartial();
        $mockOrganisation->setId(1);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockOrganisation)
            ->once()
            ->shouldReceive('save')
            ->with($mockOrganisation)
            ->once();

        $this->repoMap['Person']
            ->shouldReceive('save')
            ->with(m::type(PersonEntity::class))
            ->once()
            ->getMock();

        $this->repoMap['OrganisationPerson']
            ->shouldReceive('save')
            ->with(m::type(OrganisationPersonEntity::class))
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(1, $result->getIds()['organisation']);
        $this->assertEquals('Organisation updated successfully', $result->getMessages()[0]);
    }

    public function testHandleCommandUpdateIrfo()
    {
        $data = [
            'businessType' => OrganisationEntity::ORG_TYPE_IRFO,
            'natureOfBusiness' => [self::NATURE_OF_BUSINESS],
            'name' => 'name',
            'isIrfo' => 'Y',
            'id' => 1,
            'version' => 2,
        ];

        $command = UpdateCmd::create($data);

        $mockOrganisation = m::mock(OrganisationEntity::class)->makePartial();
        $mockOrganisation->setId(1);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockOrganisation)
            ->once()
            ->shouldReceive('save')
            ->with($mockOrganisation)
            ->once();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(1, $result->getIds()['organisation']);
        $this->assertEquals('Organisation updated successfully', $result->getMessages()[0]);
    }
}
