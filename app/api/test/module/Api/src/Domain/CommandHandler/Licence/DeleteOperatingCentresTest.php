<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteOperatingCentres as Cmd;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\DeleteOperatingCentres
 */
class DeleteOperatingCentresTest extends CommandHandlerTestCase
{
    /** @var CommandHandler\Application\DeleteOperatingCentres  */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CommandHandler\Licence\DeleteOperatingCentres();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            OperatingCentre::class => [
                1 => m::mock(OperatingCentre::class),
            ],
            Licence::class => [
                111 => m::mock(Licence::class)->makePartial(),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 111,
            'ids' => [
                123
            ]
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc1 */
        $loc1 = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc1->setId(123);
        $loc1->setOperatingCentre($this->mapReference(OperatingCentre::class, 1));
        $loc1->setLicence($this->mapReference(Licence::class, 111));

        $locs = new ArrayCollection();
        $locs->add($loc1);

        /** @var Licence $application */
        $application = m::mock(Licence::class)->makePartial();
        $application->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('delete')
            ->once()
            ->with($loc1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteConditionUndertakings::class,
            [
                'operatingCentre' => $this->mapReference(OperatingCentre::class, 1),
                'licence' => $this->mapReference(Licence::class, 111),
            ],
            (new \Dvsa\Olcs\Api\Domain\Command\Result())->addMessage('DELETE_CONDITIONS_UNDERTAKINGS')
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteApplicationLinks::class,
            ['operatingCentre' => $this->mapReference(OperatingCentre::class, 1)],
            (new \Dvsa\Olcs\Api\Domain\Command\Result())->addMessage('DELETE_OTHER_APPLICATIONS')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'DELETE_CONDITIONS_UNDERTAKINGS',
                'DELETE_OTHER_APPLICATIONS',
                '1 Operating Centre(s) removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandCannotDelete()
    {
        $data = [
            'licence' => 111,
            'ids' => [
                123
            ]
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc1 */
        $loc1 = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc1->setId(123);
        $loc1->shouldReceive('checkCanDelete')->with()->once()->andReturn(['ERROR' => 'Fooo']);

        $locs = new ArrayCollection();
        $locs->add($loc1);

        /** @var Licence $application */
        $application = m::mock(Licence::class)->makePartial();
        $application->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\BadRequestException::class);

        $this->sut->handleCommand($command);
    }
}
