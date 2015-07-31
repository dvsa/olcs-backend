<?php

/**
 * Create Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateOperatingCentres as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateOperatingCentres as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Create Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateOperatingCentresTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);

        $this->mockedSmServices['UpdateOperatingCentreHelper'] = m::mock(UpdateOperatingCentreHelper::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        parent::initReferences();
    }

    public function testHandleCommandPsvInvalid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = [$loc];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(true);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandGvInvalid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = [$loc];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(false);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPsvValid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 4,
            'totAuthVehicles' => 10,
            ''
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = [$loc];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(true);
        $licence->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->repoMap['Licence']->shouldReceive('save')
            ->once()
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Licence record updated']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandGvValid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = [$loc];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(false);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->repoMap['Licence']->shouldReceive('save')
            ->once()
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Licence record updated']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
