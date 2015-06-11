<?php

/**
 * Create Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Workshop;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\Workshop;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Workshop\CreateWorkshop;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Workshop\CreateWorkshop as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Workshop as WorkshopEntity;

/**
 * Create Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateWorkshopTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateWorkshop();
        $this->mockRepo('Workshop', Workshop::class);
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 111,
            'isExternal' => 'Y',
            'contactDetails' => [
                'fao' => 'Some name',
                'address' => [
                    'addressLine1' => '123 street',
                    'town' => 'Footown',
                    'postcode' => 'FO0 70WN'
                ]
            ]
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();

        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $expectedData = [
            'id' => null,
            'version' => null,
            'addressLine1' => '123 street',
            'addressLine2' => null,
            'addressLine3' => null,
            'addressLine4' => null,
            'town' => 'Footown',
            'postcode' => 'FO0 70WN',
            'countryCode' => null,
            'contactType' => ContactDetailsEntity::CONTACT_TYPE_WORKSHOP
        ];
        $result1 = new Result();
        $result1->addId('contactDetails', 123);
        $result1->addMessage('Address created');
        $this->expectedSideEffect(SaveAddress::class, $expectedData, $result1);

        $this->repoMap['ContactDetails']->shouldReceive('fetchById')
            ->with(123)
            ->andReturn($contactDetails)
            ->shouldReceive('save')
            ->with($contactDetails);

        /** @var WorkshopEntity $savedWorkshop */
        $savedWorkshop = null;

        $this->repoMap['Workshop']->shouldReceive('save')
            ->with(m::type(WorkshopEntity::class))
            ->andReturnUsing(
                function (WorkshopEntity $workshop) use (&$savedWorkshop) {
                    $workshop->setId(321);
                    $savedWorkshop = $workshop;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'workshop' => 321,
                'contactDetails' => 123
            ],
            'messages' => [
                'Address created',
                'Workshop created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(WorkshopEntity::class, $savedWorkshop);
        $this->assertEquals('Y', $savedWorkshop->getIsExternal());
        $this->assertSame($licence, $savedWorkshop->getLicence());
        $this->assertSame($contactDetails, $savedWorkshop->getContactDetails());

        $this->assertEquals('Some name', $contactDetails->getFao());
    }
}
