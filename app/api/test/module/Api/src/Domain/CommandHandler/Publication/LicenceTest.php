<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Doctrine\ORM\Query;
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
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * LicenceTest
 */
class LicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new \Dvsa\Olcs\Api\Domain\CommandHandler\Publication\Licence();
        $this->mockRepo('Publication', PublicationRepo::class);
        $this->mockRepo('PublicationLink', PublicationLinkRepo::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);

        $this->mockedSmServices = [
            PublicationGenerator::class => m::mock(PublicationGenerator::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            PublicationSectionEntity::class => [
                PublicationSectionEntity::LIC_REVOKED_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::LIC_SURRENDERED_SECTION => m::mock(PublicationSectionEntity::class),
                PublicationSectionEntity::LIC_TERMINATED_SECTION => m::mock(PublicationSectionEntity::class),
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::Create(
            ['id' => 510]
        );

        $publication = new PublicationEntity();

        $licence = $this->getTestingLicence();
        $licence->getStatus()->setId(LicenceEntity::LICENCE_STATUS_REVOKED);

        $publicationLink = new PublicationLinkEntity();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Publication']->shouldReceive('fetchLatestForTrafficAreaAndType')->with('T', 'N&P')->once()
            ->andReturn($publication);
        $this->repoMap['PublicationLink']->shouldReceive('fetchSingleUnpublished')->once()->andReturn($publicationLink)
            ->shouldReceive('save')->with(m::type(PublicationLinkEntity::class));

        $this->mockedSmServices[PublicationGenerator::class]
            ->shouldReceive('createPublication')->with('LicencePublication', $publicationLink, [])->once()
            ->andReturn($publicationLink);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(ResultCmd::class, $result);
    }

    public function testHandleCommandGoods()
    {
        $command = Cmd::Create(
            ['id' => 510]
        );

        $publication = new PublicationEntity();

        $licence = $this->getTestingLicence();
        $licence->getStatus()->setId(LicenceEntity::LICENCE_STATUS_REVOKED);
        $licence->setGoodsOrPsv(new RefData(LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE));

        $publicationLink = new PublicationLinkEntity();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Publication']->shouldReceive('fetchLatestForTrafficAreaAndType')->with('T', 'A&D')->once()
            ->andReturn($publication);
        $this->repoMap['PublicationLink']->shouldReceive('fetchSingleUnpublished')->once()->andReturn($publicationLink)
            ->shouldReceive('save')->with(m::type(PublicationLinkEntity::class));

        $this->mockedSmServices[PublicationGenerator::class]
            ->shouldReceive('createPublication')->with('LicencePublication', $publicationLink, [])->once()
            ->andReturn($publicationLink);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(ResultCmd::class, $result);
    }

    public function testHandleCommandMissingPubSection()
    {
        $command = Cmd::Create(
            ['id' => 510]
        );

        $publication = new PublicationEntity();

        $licence = $this->getTestingLicence();
        $licence->getStatus()->setId(LicenceEntity::LICENCE_STATUS_VALID);
        $licence->setGoodsOrPsv(new RefData(LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE));

        $publicationLink = new PublicationLinkEntity();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Publication']->shouldReceive('fetchLatestForTrafficAreaAndType')->with('T', 'A&D')->once()
            ->andReturn($publication);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ForbiddenException::class);

        $this->sut->handleCommand($command);
    }
}
