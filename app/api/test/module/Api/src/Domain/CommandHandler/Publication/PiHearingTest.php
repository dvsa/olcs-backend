<?php

/**
 * PiHearingTest
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\PiHearing;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as PiHearingRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Publication\PiHearing as PiHearingCmd;
use Dvsa\Olcs\Api\Domain\Command\Publication\PiDecision as PiDecisionCmd;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData as PoliceEntity;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Domain\Command\Result as ResultCmd;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedPi as UnpublishedPiQry;

/**
 * PiHearingTest
 */
class PiHearingTest extends CommandHandlerTestCase
{

    //variables to hold traffic area entity references
    protected $ta1;
    protected $ta2;
    protected $ta3;

    //traffic area id values
    protected $trafficArea = 'M';
    protected $trafficArea2 = 'N'; //NI
    protected $trafficArea3 = 'D';

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
        $this->ta1 = m::mock(TrafficAreaEntity::class);
        $this->ta1->shouldReceive('getId')->andReturn($this->trafficArea);

        $this->ta2 = m::mock(TrafficAreaEntity::class);
        $this->ta2->shouldReceive('getId')->andReturn($this->trafficArea2);

        $this->ta3 = m::mock(TrafficAreaEntity::class);
        $this->ta3->shouldReceive('getId')->andReturn($this->trafficArea3);

        $this->references = [
            TrafficAreaEntity::class => [
                $this->trafficArea => $this->ta1,
                $this->trafficArea2 => $this->ta2,
                $this->trafficArea3 => $this->ta3
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
     * @dataProvider commandProvider
     * @param string $cmdClass
     */
    public function testHandleNonTmHearing($cmdClass)
    {
        $id = 99;
        $isTm = false;
        $text2 = 'text2';
        $licType = LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE;
        $trafficArea = 'M';
        $publicationId = 33;
        $pi = 44;
        $piVenueId = 55;
        $piVenueOther = 'pi venue other';
        $hearingDate = \DateTime::createFromFormat('Y-m-d', '2014-03-05');

        $command = $cmdClass::Create(
            [
                'id' => $id,
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

        $licenceMock = m::mock(LicenceEntity::class);
        $licenceMock->shouldReceive('getTrafficArea')->andReturn($mockTa);
        $licenceMock->shouldReceive('getGoodsOrPsv->getId')->andReturn($licType);

        $casesMock = m::mock(CasesEntity::class);
        $casesMock->shouldReceive('isTm')->andReturn($isTm);
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

    /**
     * @dataProvider commandProvider
     *
     * @param $cmdClass
     */
    public function testHandleTmHearingCreate($cmdClass)
    {
        $id = 99;
        $isTm = true;
        $text2 = 'text2';
        $publishTrafficAreas = ['M', 'N'];
        $pubTypes = ['All'];
        $publicationId = 33;
        $pi = 44;
        $piVenueId = 55;
        $piVenueOther = 'pi venue other';
        $hearingDate = \DateTime::createFromFormat('Y-m-d', '2014-03-05');

        $allTrafficAreas = [
            0 => $this->ta1,
            1 => $this->ta2,
            2 => $this->ta3
        ];

        $command = $cmdClass::Create(
            [
                'id' => $id,
                'trafficAreas' => $publishTrafficAreas,
                'pubType' => $pubTypes,
                'text2' => $text2
            ]
        );

        $publicationLinkMock = m::mock(PublicationLinkEntity::class)->makePartial();

        $this->mockedSmServices[PublicationGenerator::class]
            ->shouldReceive('createPublication')
            ->andReturn($publicationLinkMock);

        $publicationMock = m::mock(PublicationEntity::class);
        $publicationMock->shouldReceive('getId')->andReturn($publicationId);

        $transportManagerMock = m::mock(TransportManagerEntity::class);

        $casesMock = m::mock(CasesEntity::class);
        $casesMock->shouldReceive('isTm')->andReturn($isTm);
        $casesMock->shouldReceive('getTransportManager')->andReturn($transportManagerMock);

        $piMock = m::mock(PiEntity::class);
        $piMock->shouldReceive('getCase')->andReturn($casesMock);
        $piMock->shouldReceive('getId')->andReturn($pi);

        $piHearingMock = m::mock(PiHearingEntity::class);
        $piHearingMock->shouldReceive('getPi')->andReturn($piMock);
        $piHearingMock->shouldReceive('getPiVenue->getId')->andReturn($piVenueId);
        $piHearingMock->shouldReceive('getPiVenueOther')->andReturn($piVenueOther);
        $piHearingMock->shouldReceive('getHearingDate')->andReturn($hearingDate);
        $piHearingMock->shouldReceive('getId')->andReturn($id);

        $this->repoMap['TrafficArea']->shouldReceive('fetchAll')->andReturn($allTrafficAreas);

        $this->repoMap['PiHearing']->shouldReceive('fetchUsingId')->andReturn($piHearingMock);

        $this->repoMap['Publication']->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->with('M', 'A&D')
            ->once()
            ->andReturn($publicationMock)
            ->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->with('M', 'N&P')
            ->once()
            ->andReturn($publicationMock)
            ->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->with('N', 'A&D')
            ->once()
            ->andReturn($publicationMock)
            ->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->with('D', 'A&D')
            ->once()
            ->andReturn($publicationMock)
            ->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->with('D', 'N&P')
            ->once()
            ->andReturn($publicationMock);

        $this->repoMap['PublicationLink']
            ->shouldReceive('fetchSingleUnpublished')
            ->with(m::type(UnpublishedPiQry::class))
            ->andReturn($publicationLinkMock)
            ->shouldReceive('save')
            ->with(m::type(PublicationLinkEntity::class))
            ->shouldReceive('delete')
            ->with(m::type(PublicationLinkEntity::class));

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(ResultCmd::class, $result);
    }

    /**
     * @dataProvider commandProvider
     *
     * @param $cmdClass
     */
    public function testHandleTmHearingDelete($cmdClass)
    {
        $id = 99;
        $isTm = true;
        $text2 = 'text2';
        $publishTrafficAreas = ['all'];
        $pubTypes = ['N&P'];
        $publicationId = 33;
        $pi = 44;
        $piVenueId = 55;
        $piVenueOther = 'pi venue other';
        $hearingDate = \DateTime::createFromFormat('Y-m-d', '2014-03-05');

        $allTrafficAreas = [
            0 => $this->ta2
        ];

        $command = $cmdClass::Create(
            [
                'id' => $id,
                'trafficAreas' => $publishTrafficAreas,
                'pubType' => $pubTypes,
                'text2' => $text2
            ]
        );

        $policeDataMock = m::mock(PoliceEntity::class);
        $policeDataMock->shouldReceive('setPublicationLink')->once()->with(null)->andReturnSelf();
        $policeArrayCollection = new ArrayCollection([$policeDataMock]);

        $publicationLinkMock = m::mock(PublicationLinkEntity::class)->makePartial();
        $publicationLinkMock->shouldReceive('getId')->andReturn(1);
        $publicationLinkMock->shouldReceive('getPoliceDatas')->once()->andReturn($policeArrayCollection);

        $this->mockedSmServices[PublicationGenerator::class]
            ->shouldReceive('createPublication')
            ->andReturn($publicationLinkMock);

        $publicationMock = m::mock(PublicationEntity::class);
        $publicationMock->shouldReceive('getId')->andReturn($publicationId);

        $transportManagerMock = m::mock(TransportManagerEntity::class);

        $casesMock = m::mock(CasesEntity::class);
        $casesMock->shouldReceive('isTm')->andReturn($isTm);
        $casesMock->shouldReceive('getTransportManager')->andReturn($transportManagerMock);

        $piMock = m::mock(PiEntity::class);
        $piMock->shouldReceive('getCase')->andReturn($casesMock);
        $piMock->shouldReceive('getId')->andReturn($pi);

        $piHearingMock = m::mock(PiHearingEntity::class);
        $piHearingMock->shouldReceive('getPi')->andReturn($piMock);
        $piHearingMock->shouldReceive('getPiVenue->getId')->andReturn($piVenueId);
        $piHearingMock->shouldReceive('getPiVenueOther')->andReturn($piVenueOther);
        $piHearingMock->shouldReceive('getHearingDate')->andReturn($hearingDate);
        $piHearingMock->shouldReceive('getId')->andReturn($id);

        $this->repoMap['TrafficArea']->shouldReceive('fetchAll')->andReturn($allTrafficAreas);

        $this->repoMap['PiHearing']->shouldReceive('fetchUsingId')->andReturn($piHearingMock);

        $this->repoMap['Publication']->shouldReceive('fetchLatestForTrafficAreaAndType')
            ->with('N', 'A&D')
            ->once()
            ->andReturn($publicationMock);

        $this->repoMap['PublicationLink']
            ->shouldReceive('fetchSingleUnpublished')
            ->with(m::type(UnpublishedPiQry::class))
            ->andReturn($publicationLinkMock)
            ->shouldReceive('delete')
            ->with(m::type(PublicationLinkEntity::class));

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(ResultCmd::class, $result);
    }

    /**
     * @return array
     */
    public function commandProvider()
    {
        return [
            [PiHearingCmd::class],
            [PiDecisionCmd::class]
        ];
    }
}
