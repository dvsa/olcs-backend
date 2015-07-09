<?php

/**
 * Process Application Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\ProcessApplicationOperatingCentres;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessApplicationOperatingCentres as ProcessAocCmd;
use ZfcRbac\Service\AuthorizationService;

/**
 * Process Application Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ProcessApplicationOperatingCentresTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ProcessApplicationOperatingCentres();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('LicenceOperatingCentre', \Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre::class);
        $this->mockRepo(
            'ApplicationOperatingCentre',
            \Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre::class
        );

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandAdd()
    {
        $data = [
            'id' => 111
        ];

        $command = ProcessAocCmd::create($data);

        $oc = m::mock(OperatingCentre::class)->makePartial();

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('A');
        $aoc->setIsInterim(true);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setOperatingCentre($oc);

        $aocs = [
            $aoc
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('save')
            ->with($aoc);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (LicenceOperatingCentre $loc) use ($oc) {
                    $this->assertEquals(10, $loc->getNoOfVehiclesRequired());
                    $this->assertSame($oc, $loc->getOperatingCentre());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 licence operating centre(s) created',
                '0 licence operating centre(s) updated',
                '0 licence operating centre(s) removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertFalse($aoc->getIsInterim());
    }

    public function testHandleCommandUpdate()
    {
        $data = [
            'id' => 111
        ];

        $command = ProcessAocCmd::create($data);

        $oc = m::mock(OperatingCentre::class)->makePartial();

        $aoc = new ApplicationOperatingCentre();
        $aoc->setAction('U');
        $aoc->setIsInterim(true);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setOperatingCentre($oc);

        $aocs = [
            $aoc
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $loc = new LicenceOperatingCentre($licence, $oc);

        $locs = new ArrayCollection();
        $locs->add($loc);

        $licence->shouldReceive('getOperatingCentres->matching')
            ->andReturn($locs);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('save')
            ->with($aoc);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (LicenceOperatingCentre $loc) use ($oc) {
                    $this->assertEquals(10, $loc->getNoOfVehiclesRequired());
                    $this->assertSame($oc, $loc->getOperatingCentre());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '0 licence operating centre(s) created',
                '1 licence operating centre(s) updated',
                '0 licence operating centre(s) removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertFalse($aoc->getIsInterim());
    }

    public function testHandleCommandDelete()
    {
        $data = [
            'id' => 111
        ];

        $command = ProcessAocCmd::create($data);

        $oc = m::mock(OperatingCentre::class)->makePartial();

        $aoc = new ApplicationOperatingCentre();
        $aoc->setAction('D');
        $aoc->setIsInterim(true);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setOperatingCentre($oc);

        $aocs = [
            $aoc
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $loc = new LicenceOperatingCentre($licence, $oc);

        $locs = new ArrayCollection();
        $locs->add($loc);

        $licence->shouldReceive('getOperatingCentres->matching')
            ->andReturn($locs);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('save')
            ->with($aoc);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('delete')
            ->once()
            ->with($loc);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '0 licence operating centre(s) created',
                '0 licence operating centre(s) updated',
                '1 licence operating centre(s) removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertFalse($aoc->getIsInterim());
    }

    public function testHandleCommandDeleteException()
    {
        $this->setExpectedException(\Exception::class);

        $data = [
            'id' => 111
        ];

        $command = ProcessAocCmd::create($data);

        $oc = m::mock(OperatingCentre::class)->makePartial();

        $aoc = new ApplicationOperatingCentre();
        $aoc->setAction('D');
        $aoc->setIsInterim(true);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setOperatingCentre($oc);

        $aocs = [
            $aoc
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $locs = new ArrayCollection();

        $licence->shouldReceive('getOperatingCentres->matching')
            ->andReturn($locs);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('save')
            ->with($aoc);

        $this->sut->handleCommand($command);
    }
}
