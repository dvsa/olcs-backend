<?php

/** PiHearingTest
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\PiHearing;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as PiHearingRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Publication\PiHearing as Cmd;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Domain\Command\Result as ResultCmd;

/**
 * PiHearingTest
 */
class PiHearingTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PiHearing();
        $this->mockRepo('Publication', PublicationRepo::class);
        $this->mockRepo('PublicationLink', PublicationLinkRepo::class);
        $this->mockRepo('PiHearing', PiHearingRepo::class);
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
                PublicationSectionEntity::TM_HEARING_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::TM_DECISION_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::HEARING_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::DECISION_SECTION => m::mock(PublicationSectionEntity::class)
            ]
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand
     *
     * @dataProvider handleCommandProvider
     * @param Bool $isTm
     */
    public function testHandleCommand($isTm)
    {
        $id = 99;
        $text2 = 'text2';
        $licType = LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE;
        $pubTypes = ['All'];
        $trafficArea = 'M';
        $trafficAreas = [0 => $trafficArea];
        $publicationId = 33;
        $pi = 44;
        $piVenueId = 55;
        $piVenueOther = 'pi venue other';
        $hearingDate = '2014-03-05';

        $command = Cmd::Create(
            [
                'id' => $id,
                'trafficAreas' => $trafficAreas,
                'pubTypes' => $pubTypes,
                'text2' => $text2
            ]
        );

        $publicationLinkMock = m::mock(PublicationLinkEntity::class)->makePartial();

        $this->mockedSmServices[PublicationGenerator::class]
            ->shouldReceive('createPublication')
            ->andReturn($publicationLinkMock);

        $publicationMock = m::mock(PublicationEntity::class);
        $publicationMock->shouldReceive('getId')->andReturn($publicationId);

        $mockTa = m::mock(TrafficAreaEntity::class);
        $mockTa->shouldReceive('getId')->andReturn($trafficArea);

        $transportManagerMock = m::mock(TransportManagerEntity::class);

        $licenceMock = m::mock(LicenceEntity::class);
        $licenceMock->shouldReceive('getTrafficArea')->andReturn($mockTa);
        $licenceMock->shouldReceive('getGoodsOrPsv->getId')->andReturn($licType);

        $casesMock = m::mock(CasesEntity::class);
        $casesMock->shouldReceive('isTm')->andReturn($isTm);
        $casesMock->shouldReceive('getTransportManager')->andReturn($transportManagerMock);
        $casesMock->shouldReceive('getLicence')->andReturn($licenceMock);

        $piMock = m::mock(PiEntity::class);
        $piMock->shouldReceive('getCase')->andReturn($casesMock);
        $piMock->shouldReceive('getId')->andReturn($pi);

        $piHearingMock = m::mock(PiHearingEntity::class);
        $piHearingMock->shouldReceive('getPi')->andReturn($piMock);
        $piHearingMock->shouldReceive('getPiVenue->getId')->andReturn($piVenueId);
        $piHearingMock->shouldReceive('getPiVenueOther')->andReturn($piVenueOther);
        $piHearingMock->shouldReceive('getHearingDate')->andReturn($hearingDate);
        $piHearingMock->shouldReceive('getId')->andReturn($id);

        $this->repoMap['PiHearing']->shouldReceive('fetchUsingId')->andReturn($piHearingMock);

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
            [true],
            [false]
        ];
    }
}
