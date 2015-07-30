<?php

/**
 * Delete Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\DeleteOperatingCentres as Cmd;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\DeleteOperatingCentres as CommandHandler;
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
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);

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
            'application' => 111,
            'ids' => [
                123
            ]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationOperatingCentre $aoc1 */
        $aoc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc1->setId(123);

        /** @var ApplicationOperatingCentre $aoc2 */
        $aoc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc2->setId(321);

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('delete')
            ->once()
            ->with($aoc1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Operating Centre(s) removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandRemovingAllOcs()
    {
        $data = [
            'application' => 111,
            'ids' => [
                123
            ]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationOperatingCentre $aoc1 */
        $aoc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc1->setId(123);

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('setEnforcementArea')
            ->once()
            ->with(null)
            ->shouldReceive('setTrafficArea')
            ->once()
            ->with(null);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('delete')
            ->once()
            ->with($aoc1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Operating Centre(s) removed',
                'Updated traffic area',
                'Updated enforcement area',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
