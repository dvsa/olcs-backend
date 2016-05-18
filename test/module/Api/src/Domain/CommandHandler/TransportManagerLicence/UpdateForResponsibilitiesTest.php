<?php

/**
 * TransportManagerLicence / UpdateForResponsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerLicence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerLicence\UpdateForResponsibilities as UpdateForResp;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TransportManagerLicenceRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\UpdateForResponsibilities as Cmd;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;
use Doctrine\ORM\Query;

/**
 * TransportManagerLicence / UpdateForResponsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UpdateForResponsibilitiesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateForResp();
        $this->mockRepo('TransportManagerLicence', TransportManagerLicenceRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'tmType'
        ];

        $this->references = [
            OperatingCentreEntity::class => [
                1 => m::mock(OperatingCentreEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 1;
        $data = [
            'id' => $id,
            'version' => 2,
            'tmType' => 'tmType',
            'isOwner' => 1,
            'hoursMon' => 1,
            'hoursTue' => 2,
            'hoursWed' => 3,
            'hoursThu' => 4,
            'hoursFri' => 5,
            'hoursSat' => 6,
            'hoursSun' => 7,
            'additionalInformation' => 'ai',
            'operatingCentres' => [1]
        ];

        $command = Cmd::create($data);

        $mockTmLicence = m::mock(TransportManagerLicenceEntity::class)
            ->shouldReceive('getOperatingCentres')
            ->andReturn(
                m::mock()
                ->shouldReceive('clear')
                ->once()
                ->shouldReceive('add')
                ->with($this->references[OperatingCentreEntity::class][1])
                ->once()
                ->getMock()
            )
            ->twice()
            ->shouldReceive('updateTransportManagerLicence')
            ->with(
                $this->refData['tmType'],
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                'ai',
                1
            )
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id)
            ->once()
            ->getMock();

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockTmLicence)
            ->once()
            ->shouldReceive('save')
            ->with($mockTmLicence)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(
            $result->toArray(),
            [
                'id' => [
                    'transportManagerLicence' => 1
                ],
                'messages' => [
                    'Transport Manager Licence updated'
                ]
            ]
        );
    }
}
