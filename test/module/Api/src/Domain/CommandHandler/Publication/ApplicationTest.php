<?php

/** PiHearingTest
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\Application;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Publication\Application as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Domain\Command\Result as ResultCmd;

/**
 * ApplicationTest
 */
class ApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Application();
        $this->mockRepo('Publication', PublicationRepo::class);
        $this->mockRepo('PublicationLink', PublicationLinkRepo::class);
        $this->mockRepo('Application', ApplicationRepo::class);
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
                PublicationSectionEntity::APP_NEW_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::APP_GRANTED_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::APP_REFUSED_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::APP_WITHDRAWN_SECTION => m::mock(PublicationSectionEntity::class)
            ]
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand
     *
     * @dataProvider handleCommandProvider
     * @param String $appStatus
     */
    public function testHandleCommand($appStatus)
    {
        $id = 99;
        $licenceId = 88;
        $licType = LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE;
        $trafficArea = 'M';
        $publicationId = 33;

        $command = Cmd::Create(
            [
                'id' => $id,
                'trafficArea' => $trafficArea
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
        $licenceMock->shouldReceive('getId')->andReturn($licenceId);

        $applicationMock = m::mock(ApplicationEntity::class);
        $applicationMock->shouldReceive('getLicence')->andReturn($licenceMock);
        $applicationMock->shouldReceive('getLicence->getId')->andReturn($licenceId);
        $applicationMock->shouldReceive('getStatus->getId')->andReturn($appStatus);
        $applicationMock->shouldReceive('getGoodsOrPsv->getId')->andReturn($licType);
        $applicationMock->shouldReceive('getId')->andReturn($id);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->andReturn($applicationMock);

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
     * @dataProvider publicationSectionIdProvider
     *
     * @param string $appStatus
     * @param int $expectedSection
     */
    public function testGetPublicationSectionId($appStatus, $expectedSection)
    {
        $this->assertEquals($expectedSection, $this->sut->getPublicationSectionId($appStatus));
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testInvalidSectionIdException()
    {
        $this->sut->getPublicationSectionId('some_status');
    }

    /**
     * Data provider for handleCommand
     *
     * @return array
     */
    public function handleCommandProvider()
    {
        return [
            [ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION],
            [ApplicationEntity::APPLICATION_STATUS_GRANTED],
            [ApplicationEntity::APPLICATION_STATUS_REFUSED],
            [ApplicationEntity::APPLICATION_STATUS_WITHDRAWN]
        ];
    }

    /**
     * Data provider for testGetPublicationSectionId
     *
     * @return array
     */
    public function publicationSectionIdProvider()
    {
        return [
            [ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION, PublicationSectionEntity::APP_NEW_SECTION],
            [ApplicationEntity::APPLICATION_STATUS_GRANTED, PublicationSectionEntity::APP_GRANTED_SECTION],
            [ApplicationEntity::APPLICATION_STATUS_REFUSED, PublicationSectionEntity::APP_REFUSED_SECTION],
            [ApplicationEntity::APPLICATION_STATUS_WITHDRAWN, PublicationSectionEntity::APP_WITHDRAWN_SECTION]
        ];
    }
}
