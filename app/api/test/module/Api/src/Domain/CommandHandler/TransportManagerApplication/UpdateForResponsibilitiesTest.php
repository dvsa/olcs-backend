<?php

/**
 * TransportManagerApplication / UpdateForResponsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\UpdateForResponsibilities as UpdateForResp;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as TransportManagerApplicationRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateForResponsibilities as Cmd;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;
use Doctrine\ORM\Query;

/**
 * TransportManagerApplication / UpdateForResponsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UpdateForResponsibilitiesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateForResp();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplicationRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'tmType', 'tmAppStatus'
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
            'tmApplicationStatus' => 'tmAppStatus',
            'operatingCentres' => [1]
        ];

        $command = Cmd::create($data);

        $mockTmApplication = m::mock(TransportManagerApplicationEntity::class)
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
            ->shouldReceive('updateTransportManagerApplicationFull')
            ->with(
                $this->refData['tmType'],
                1,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                'ai',
                $this->refData['tmAppStatus']
            )
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id)
            ->once()
            ->getMock();

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($mockTmApplication)
            ->once()
            ->shouldReceive('save')
            ->with($mockTmApplication)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(
            $result->toArray(),
            [
                'id' => [
                    'transportManagerApplication' => 1
                ],
                'messages' => [
                    'Transport Manager Application updated'
                ]
            ]
        );
    }
}
