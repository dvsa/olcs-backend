<?php

/**
 * Delete Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteOperatingCentres as Cmd;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\DeleteOperatingCentres as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Delete Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteOperatingCentresTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

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

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
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
