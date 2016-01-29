<?php

/**
 * Update Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\VariationOperatingCentre;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\ApplicationOperatingCentre\Update as AppUpdate;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\VariationOperatingCentre\Update as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\VariationOperatingCentre\Update as CommandHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Update Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);
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
            'id' => 'A111',
            'address' => [
                'id' => 123
            ]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('U');

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($aoc);

        $data = $command->getArrayCopy();
        $data['id'] = 111;
        $data['address'] = null;
        unset($data['application']);
        $result1 = new Result();
        $result1->addMessage('AppUpdate');
        $this->expectedSideEffect(AppUpdate::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'AppUpdate'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForbidden()
    {
        $data = [
            'id' => 'A111',
            'address' => [
                'id' => 123
            ]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('D');

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($aoc);

        $this->setExpectedException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandLicence()
    {
        $data = [
            'id' => 'L111',
            'application' => 222,
            'address' => [
                'id' => 123
            ]
        ];
        $command = Cmd::create($data);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        $aoc = m::mock(ApplicationOperatingCentre::class);

        $deltaRecords = new ArrayCollection();
        $deltaRecords->add($aoc);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('getDeltaAocByOc')
            ->with($oc)
            ->andReturn($deltaRecords);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($application);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setOperatingCentre($oc);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($loc);

        $this->setExpectedException(ForbiddenException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandLicenceWithoutDeltas()
    {
        $data = [
            'id' => 'L111',
            'application' => 222,
            'address' => [
                'id' => 123
            ]
        ];
        $command = Cmd::create($data);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        $deltaRecords = new ArrayCollection();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('getDeltaAocByOc')
            ->with($oc)
            ->andReturn($deltaRecords);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($application);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setOperatingCentre($oc);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($loc);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('save')
            ->once()
            ->with(m::type(ApplicationOperatingCentre::class));

        $data = $command->getArrayCopy();
        $data['id'] = null;
        $data['version'] = 1;
        $data['address'] = null;
        unset($data['application']);
        $result1 = new Result();
        $result1->addMessage('AppUpdate');
        $this->expectedSideEffect(AppUpdate::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'AppUpdate'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
