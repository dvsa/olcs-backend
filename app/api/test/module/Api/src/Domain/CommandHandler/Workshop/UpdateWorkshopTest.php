<?php

/**
 * Update Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Workshop;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Workshop;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Workshop\UpdateWorkshop;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Workshop\UpdateWorkshop as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Workshop as WorkshopEntity;

/**
 * Update Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateWorkshopTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateWorkshop();
        $this->mockRepo('Workshop', Workshop::class);

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
            'id' => 123,
            'version' => 1,
            'contactDetails' => [
                'id' => 222,
                'version' => 1,
                'fao' => 'Some name',
                'address' => [
                    'id' => 111,
                    'version' => 1,
                    'addressLine1' => '123 street',
                    'town' => 'Footown',
                    'postcode' => 'FO0 70WN'
                ]
            ]
        ];
        $command = Cmd::create($data);

        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = m::mock(ContactDetailsEntity::class)->makePartial();

        /** @var WorkshopEntity $workshop */
        $workshop = m::mock(WorkshopEntity::class)->makePartial();
        $workshop->setContactDetails($contactDetails);

        $this->repoMap['Workshop']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($workshop)
            ->shouldReceive('save')
            ->with($workshop);

        $expectedData = [
            'id' => 111,
            'version' => 1,
            'addressLine1' => '123 street',
            'addressLine2' => null,
            'addressLine3' => null,
            'addressLine4' => null,
            'town' => 'Footown',
            'postcode' => 'FO0 70WN',
            'countryCode' => null,
            'contactType' => null
        ];
        $result1 = new Result();
        $result1->addMessage('Address updated');
        $this->expectedSideEffect(SaveAddress::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Address updated',
                'Workshop updated'
            ],
            'flags' => [
                'hasChanged' => false
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
