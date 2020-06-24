<?php

/**
 * Set Default Traffic Area And Enforcement Area Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\SetDefaultTrafficAreaAndEnforcementArea as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Application\SetDefaultTrafficAreaAndEnforcementArea as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Address\Service\AddressInterface;

/**
 * Set Default Traffic Area And Enforcement Area Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SetDefaultTrafficAreaAndEnforcementAreaTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('OperatingCentre', Repository\OperatingCentre::class);
        $this->mockRepo('AdminAreaTrafficArea', Repository\AdminAreaTrafficArea::class);
        $this->mockRepo('PostcodeEnforcementArea', Repository\PostcodeEnforcementArea::class);

        $this->mockedSmServices['AddressService'] = m::mock(AddressInterface::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            TrafficArea::class => [
                TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE => m::mock(TrafficArea::class),
                TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE => m::mock(TrafficArea::class)
            ],
            EnforcementArea::class => [
                EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE => m::mock(EnforcementArea::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithEaAndTm()
    {
        $command = Cmd::create(['id' => 111, 'operatingCentre' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setEnforcementArea(
            $this->references[EnforcementArea::class][EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE]
        );
        $licence->setTrafficArea(
            $this->references[TrafficArea::class][TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE]
        );

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNi()
    {
        $command = Cmd::create(['id' => 111, 'operatingCentre' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(123);
        $licence->setVersion(1);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setNiFlag('Y');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result = new Result();
        $result->addMessage('Traffic area updated');
        $data = [
            'id' => 123,
            'version' => 1,
            'trafficArea' => TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
        ];
        $this->expectedSideEffect(UpdateTrafficArea::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Traffic area updated',
                'Enforcement area updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->references[EnforcementArea::class][EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE],
            $licence->getEnforcementArea()
        );
    }

    public function testHandleCommandWithMultipleOcs()
    {
        $command = Cmd::create(['id' => 111, 'operatingCentre' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $aoc1 = m::mock();
        $aoc2 = m::mock();

        $aocs = new ArrayCollection();
        $aocs->add($aoc1);
        $aocs->add($aoc2);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setNiFlag('N');
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111, 'operatingCentre' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(123);
        $licence->setVersion(1);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('getOperatingCentres->count')->andReturn(1);
        $application->setLicence($licence);
        $application->setNiFlag('N');

        /** @var OperatingCentre $operatingCentre */
        $operatingCentre = m::mock(OperatingCentre::class)->makePartial();
        $operatingCentre->shouldReceive('getAddress->getPostcode')
            ->andReturn('AB1 1BA');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $this->repoMap['OperatingCentre']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($operatingCentre);

        $this->mockedSmServices['AddressService']->shouldReceive('fetchTrafficAreaByPostcode')
            ->once()
            ->with('AB1 1BA', $this->repoMap['AdminAreaTrafficArea'])
            ->andReturn($this->references[TrafficArea::class][TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE])
            ->shouldReceive('fetchEnforcementAreaByPostcode')
            ->once()
            ->with('AB1 1BA', $this->repoMap['PostcodeEnforcementArea'])
            ->andReturn(
                $this->references[EnforcementArea::class][EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE]
            );

        $result = new Result();
        $result->addMessage('Traffic area updated');
        $data = [
            'id' => 123,
            'version' => 1,
            'trafficArea' => TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE
        ];
        $this->expectedSideEffect(UpdateTrafficArea::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Traffic area updated',
                'Enforcement area updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->references[EnforcementArea::class][EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE],
            $licence->getEnforcementArea()
        );
    }

    public function testHandleCommandWithNoAddressServiceWorkingSetEa()
    {
        $command = Cmd::create(['id' => 111, 'operatingCentre' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE]);
        $licence->setEnforcementArea(null);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setNiFlag('N');
        $application->shouldReceive('getOperatingCentres->count')->andReturn(1)->once();

        /** @var OperatingCentre $operatingCentre */
        $operatingCentre = m::mock(OperatingCentre::class)->makePartial();
        $operatingCentre->shouldReceive('getAddress->getPostcode')
            ->andReturn('SW1A 1AA');

        $this->repoMap['OperatingCentre']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($operatingCentre);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->mockedSmServices['AddressService']
            ->shouldReceive('fetchEnforcementAreaByPostcode')
            ->with('SW1A 1AA', $this->repoMap['PostcodeEnforcementArea'])
            ->once()
            ->andThrow(new \Exception);
        $result = $this->sut->handleCommand($command);
        $expected = [
            'id' => [],
            'messages' => [],
        ];
        $this->assertEquals($result->toArray(), $expected);
    }

    public function testHandleCommandWithNoAddressServiceWorkingSetTa()
    {
        $command = Cmd::create(['id' => 111, 'operatingCentre' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setTrafficArea(null);
        $licence->setEnforcementArea(
            $this->references[EnforcementArea::class][EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE]
        );

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setNiFlag('N');
        $application->shouldReceive('getOperatingCentres->count')->andReturn(1)->once();

        /** @var OperatingCentre $operatingCentre */
        $operatingCentre = m::mock(OperatingCentre::class)->makePartial();
        $operatingCentre->shouldReceive('getAddress->getPostcode')
            ->andReturn('SW1A 1AA');

        $this->repoMap['OperatingCentre']->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($operatingCentre);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->mockedSmServices['AddressService']
            ->shouldReceive('fetchTrafficAreaByPostcode')
            ->with('SW1A 1AA', $this->repoMap['AdminAreaTrafficArea'])
            ->once()
            ->andThrow(new \Exception);
        $result = $this->sut->handleCommand($command);
        $expected = [
            'id' => [],
            'messages' => [],
        ];
        $this->assertEquals($result->toArray(), $expected);
    }
}
