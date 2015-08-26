<?php

/**
 * Delete Transport Manager Links Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OperatingCentre;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteTmLinks as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\OperatingCentre\DeleteTmLinks as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Delete Transport Manager Links Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DeleteTmLinksTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('OperatingCentre', Repository\OperatingCentre::class);
        $this->mockRepo('TransportManagerApplication', Repository\TransportManagerApplication::class);
        $this->mockRepo('TransportManagerLicence', Repository\TransportManagerLicence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            OperatingCentre::class => [
                1 => m::mock(OperatingCentre::class),
            ],
            TransportManagerLicence::class => [
                11 => m::mock(TransportManagerLicence::class)->makePartial(),
                22 => m::mock(TransportManagerLicence::class)->makePartial(),
            ],
            TransportManagerApplication::class => [
                111 => m::mock(TransportManagerApplication::class)->makePartial(),
                222 => m::mock(TransportManagerApplication::class)->makePartial(),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $oc = $this->mapReference(OperatingCentre::class, 1);
        $oc->setTransportManagerLicences(
            new ArrayCollection(
                [
                    $this->mapReference(TransportManagerLicence::class, 11),
                    $this->mapReference(TransportManagerLicence::class, 22)
                ]
            )
        );

        $this->mapReference(TransportManagerLicence::class, 11)
            ->shouldReceive('getOperatingCentres->removeElement')
            ->once()
            ->with($oc);

        $this->mapReference(TransportManagerLicence::class, 22)
            ->shouldReceive('getOperatingCentres->removeElement')
            ->with($oc)
            ->once();

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('save')
            ->with($this->mapReference(TransportManagerLicence::class, 11))
            ->once()
            ->shouldReceive('save')
            ->with($this->mapReference(TransportManagerLicence::class, 22))
            ->once();

        $oc->setTransportManagerApplications(
            new ArrayCollection(
                [
                    $this->mapReference(TransportManagerApplication::class, 111),
                    $this->mapReference(TransportManagerApplication::class, 222)
                ]
            )
        );

        $this->mapReference(TransportManagerApplication::class, 111)
            ->shouldReceive('getApplication->isUnderConsideration')
            ->once()
            ->andReturn(true);
        $this->mapReference(TransportManagerApplication::class, 111)
            ->shouldReceive('getOperatingCentres->removeElement')
            ->with($oc)
            ->once();

        $this->mapReference(TransportManagerApplication::class, 222)
            ->shouldReceive('getApplication->isUnderConsideration')
            ->once()
            ->andReturn(false);

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('save')
            ->with($this->mapReference(TransportManagerApplication::class, 111))
            ->once();

        $data = ['operatingCentre' => $oc];

        $command = Cmd::create($data);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Delinked 2 TransportManagerLicence record(s) from Operating Centre 1',
                'Delinked 1 TransportManagerApplication record(s) from Operating Centre 1',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
