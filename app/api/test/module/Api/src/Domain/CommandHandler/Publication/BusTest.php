<?php

/** BusTest
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\Bus;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Publication\Bus as Cmd;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Domain\Command\Result as ResultCmd;

/**
 * BusTest
 */
class BusTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Bus();
        $this->mockRepo('Publication', PublicationRepo::class);
        $this->mockRepo('PublicationLink', PublicationLinkRepo::class);
        $this->mockRepo('Bus', BusRepo::class);
        $this->mockRepo('TrafficArea', TrafficAreaRepo::class);

        $this->mockedSmServices = [
            PublicationGenerator::class => m::mock(PublicationGenerator::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            TrafficAreaEntity::class => [
                'M' => m::mock(TrafficAreaEntity::class)
            ],
            PublicationSectionEntity::class => [
                PublicationSectionEntity::BUS_NEW_SHORT_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::BUS_NEW_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::BUS_VAR_SHORT_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::BUS_VAR_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::BUS_CANCEL_SHORT_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::BUS_CANCEL_SECTION => m::mock(PublicationSectionEntity::class)
            ]
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand throws exception on incorrect status
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testHandleCommandThrowsCorrectException()
    {
        $id = 99;
        $regNo = 44;
        $revertStatus = 'some_status';
        $shortNotice = 'Y';
        $command = Cmd::Create(['id' => $id]);

        $mockTa = m::mock(TrafficAreaEntity::class);

        $trafficAreas = new ArrayCollection([$mockTa]);

        $licenceMock = m::mock(LicenceEntity::class);

        $busMock = m::mock(BusRegEntity::class);
        $busMock->shouldReceive('getLicence')->andReturn($licenceMock);
        $busMock->shouldReceive('getRevertStatus->getId')->andReturn($revertStatus);
        $busMock->shouldReceive('getTrafficAreas')->andReturn($trafficAreas);
        $busMock->shouldReceive('getRegNo')->andReturn($regNo);
        $busMock->shouldReceive('getIsShortNotice')->andReturn($shortNotice);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')->andReturn($busMock);

        $this->sut->handleCommand($command);
    }

    /**
     * testHandleCommand
     *
     * @dataProvider handleCommandProvider
     * @param string $revertStatus
     */
    public function testHandleCommand($revertStatus, $shortNotice)
    {
        $id = 99;
        $publicationId = 33;
        $regNo = 44;
        $trafficArea = 'M';

        $command = Cmd::Create(['id' => $id]);

        $publicationLinkMock = m::mock(PublicationLinkEntity::class)->makePartial();

        $this->mockedSmServices[PublicationGenerator::class]
            ->shouldReceive('createPublication')
            ->andReturn($publicationLinkMock);

        $publicationMock = m::mock(PublicationEntity::class);
        $publicationMock->shouldReceive('getId')->andReturn($publicationId);

        $mockTa = m::mock(TrafficAreaEntity::class);
        $mockTa->shouldReceive('getId')->andReturn($trafficArea);

        $trafficAreas = new ArrayCollection([$mockTa]);

        $licenceMock = m::mock(LicenceEntity::class);

        $busMock = m::mock(BusRegEntity::class);
        $busMock->shouldReceive('getLicence')->andReturn($licenceMock);
        $busMock->shouldReceive('getRevertStatus->getId')->andReturn($revertStatus);
        $busMock->shouldReceive('getTrafficAreas')->andReturn($trafficAreas);
        $busMock->shouldReceive('getRegNo')->andReturn($regNo);
        $busMock->shouldReceive('getId')->andReturn($id);
        $busMock->shouldReceive('getIsShortNotice')->andReturn($shortNotice);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')->andReturn($busMock);

        $this->repoMap['Publication']->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->andReturn($publicationMock);

        $this->repoMap['PublicationLink']->shouldReceive('fetchSingleUnpublished')
            ->andReturn($publicationLinkMock)
            ->shouldReceive('save')
            ->with(m::type(PublicationLinkEntity::class));

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(ResultCmd::class, $result);
    }

    public function handleCommandProvider()
    {
        return [
            [BusRegEntity::STATUS_NEW, 'Y'],
            [BusRegEntity::STATUS_NEW, 'N'],
            [BusRegEntity::STATUS_VAR, 'Y'],
            [BusRegEntity::STATUS_VAR, 'N'],
            [BusRegEntity::STATUS_CANCEL, 'Y'],
            [BusRegEntity::STATUS_CANCEL, 'N']
        ];
    }
}
