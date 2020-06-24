<?php

/**
 * Transport Manager / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Tm;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\Update;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Tm\Update as Cmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;
use Dvsa\Olcs\Api\Domain\Command\Person\UpdateFull as UpdatePersonCmd;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * Transport Manager / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Update();
        $this->mockRepo('TransportManager', TransportManagerRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT,
            TransportManagerEntity::TRANSPORT_MANAGER_TYPE_BOTH
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 1;
        $data = [
            'id' => $id,
            'version' => 2,
            'type' => TransportManagerEntity::TRANSPORT_MANAGER_TYPE_BOTH,
            'status' => TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT,
            'firstName' => 'fn',
            'lastName' => 'ln',
            'birthDate' => '2015-01-01',
            'birthPlace' => 'bp',
            'title' => 'title_mr',
            'emailAddress' => 'email@address.com',
            'homeCdId' => 3,
            'homeCdVersion' => 4,
            'personId' => 5,
            'personVersion' => 6,
            'homeAddressId' => 7,
            'homeAddressVersion' => 8,
            'workAddressId' => null,
            'workAddressVersion' => null,
            'homeAddressLine1' => 'hal1',
            'homeAddressLine2' => 'hal2',
            'homeAddressLine3' => 'hal3',
            'homeAddressLine4' => 'hal4',
            'homeTown' => 'ht',
            'homePostcode' => 'hpc',
            'homeCountryCode' => 'hcc',
            'workAddressLine1' => 'wal1',
            'workAddressLine2' => 'wal2',
            'workAddressLine3' => 'wal3',
            'workAddressLine4' => 'wal4',
            'workTown' => 'wt',
            'workPostcode' => 'wpc',
            'workCountryCode' => 'wcc'
        ];

        $command = Cmd::create($data);

        $personResult = new Result();
        $personResult->addId('person', $data['personId']);
        $personResult->addMessage('Person updated');
        $this->expectedSideEffect(
            UpdatePersonCmd::class,
            [
                'id'         => $data['personId'],
                'version'    => $data['personVersion'],
                'firstName'  => $data['firstName'],
                'lastName'   => $data['lastName'],
                'title'      => $data['title'],
                'birthDate'  => $data['birthDate'],
                'birthPlace' => $data['birthPlace']
            ],
            $personResult
        );

        $workAddressResult = new Result();
        $workAddressResult->setFlag('hasChanged', true);
        $workAddressResult->addId('address', 10);
        $this->expectedSideEffect(
            SaveAddressCmd::class,
            [
                'id'           => $data['workAddressId'],
                'version'      => $data['workAddressVersion'],
                'addressLine1' => $data['workAddressLine1'],
                'addressLine2' => $data['workAddressLine2'],
                'addressLine3' => $data['workAddressLine3'],
                'addressLine4' => $data['workAddressLine4'],
                'town'         => $data['workTown'],
                'postcode'     => $data['workPostcode'],
                'countryCode'  => $data['workCountryCode'],
                'contactType'  => 'ct_tm',
            ],
            $workAddressResult
        );

        $homeAddressResult = new Result();
        $homeAddressResult->setFlag('hasChanged', true);
        $this->expectedSideEffect(
            SaveAddressCmd::class,
            [
                'id'           => $data['homeAddressId'],
                'version'      => $data['homeAddressVersion'],
                'addressLine1' => $data['homeAddressLine1'],
                'addressLine2' => $data['homeAddressLine2'],
                'addressLine3' => $data['homeAddressLine3'],
                'addressLine4' => $data['homeAddressLine4'],
                'town'         => $data['homeTown'],
                'postcode'     => $data['homePostcode'],
                'countryCode'  => $data['homeCountryCode'],
                'contactType'  => 'ct_tm',
            ],
            $homeAddressResult
        );

        $mockContactDetails = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('updateContactDetailsWithPersonAndEmailAddress')
            ->with(null, $data['emailAddress'])
            ->once()
            ->shouldReceive('getVersion')
            ->andReturn(5)
            ->once()
            ->shouldReceive('getId')
            ->andReturn($data['homeCdId'])
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']
            ->shouldReceive('fetchById')
            ->with($data['homeCdId'])
            ->andReturn($mockContactDetails)
            ->once()
            ->shouldReceive('save')
            ->with($mockContactDetails)
            ->once()
            ->getMock();

        $mockTransportManager = m::mock(TransportManagerEntity::class)
            ->shouldReceive('updateTransportManager')
            ->with(
                m::type(RefData::class),
                m::type(RefData::class),
                null
            )
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id)
            ->getMock();

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($mockTransportManager)
            ->once()
            ->shouldReceive('save')
            ->with($mockTransportManager)
            ->once()
            ->getMock();

        $this->expectedQueueSideEffect($id, Queue::TYPE_UPDATE_NYSIIS_TM_NAME, ['id' => $id]);

        $result = $this->sut->handleCommand($command);

        $res = $result->toArray();
        $this->assertEquals($res['id']['transportManager'], $id);
        $this->assertEquals($res['id']['homeAddress'], $data['homeAddressId']);
        $this->assertEquals($res['id']['workAddress'], 10);
        $this->assertEquals($res['id']['homeContactDetails'], $data['homeCdId']);
        $this->assertEquals($res['id']['person'], $data['personId']);
    }
}
