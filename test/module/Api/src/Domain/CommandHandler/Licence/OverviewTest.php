<?php

/**
 * Overview Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\Overview;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Transfer\Command\Licence\Overview as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Overview Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OverviewTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Overview();
        $this->mockRepo('Licence', Licence::class);

        $this->references = [
            LicenceEntity::class => [
                69 => m::mock(LicenceEntity::class),
            ],
            OrganisationEntity::class => [
                1 => m::mock(OrganisationEntity::class)
            ],
            TrafficAreaEntity::class => [
                'B' => m::mock(TrafficAreaEntity::class)
            ],
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $licenceId  = 69;
        $organisationId = 1;
        $version        = 10;

        $command = Cmd::create(
            [
                'id' => $licenceId,
                'version' => $version,
                'leadTcArea' => 'B',
                'reviewDate' => '2015-06-10',
                'expiryDate' => '2016-01-02',
                'translateToWelsh' => 'Y',
            ]
        );

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);

        /** @var OrganisationEntity $organisation */
        $organisation = $this->mapReference(OrganisationEntity::class, $organisationId);

        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($licence);

        $this->repoMap['Licence']
            ->shouldReceive('save')
            ->with($licence)
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'licence' => $licenceId,
            ],
            'messages' => [
                'Licence updated',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('2015-06-10', $licence->getReviewDate()->format('Y-m-d'));
        $this->assertEquals('2016-01-02', $licence->getExpiryDate()->format('Y-m-d'));
        $this->assertEquals('Y', $licence->getTranslateToWelsh());
        $this->assertEquals(
            $this->mapReference(TrafficAreaEntity::class, 'B'),
            $licence->getOrganisation()->getLeadTcArea()
        );
    }
}
