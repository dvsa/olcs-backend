<?php

/**
 * Print PSV discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PsvDisc;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\PsvDisc\PrintDiscs;
use Dvsa\Olcs\Api\Domain\Repository\DiscSequened as DiscSequenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\PsvDisc as PsvDiscRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\PsvDisc\PrintDiscs as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs as PrintDiscsCommand;
use Dvsa\Olcs\Api\Domain\Command\Discs\CreatePsvVehicleListForDiscs as CreatePsvVehicleListForDiscsCommand;

/**
 * Print PSV discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrintDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintDiscs();
        $this->mockRepo('DiscSequence', DiscSequenceRepo::class);
        $this->mockRepo('PsvDisc', PsvDiscRepo::class);

        parent::setUp();
    }

    public function testHandleCommandNoDiscsToPrint()
    {
        $this->setExpectedException(ValidationException::class);

        $niFlag = 'N';
        $licenceType = 'ltyp_r';
        $startNumber = 1;
        $discSequence = 2;
        $data = [
            'niFlag' => $niFlag,
            'licenceType' => $licenceType,
            'startNumber' => $startNumber,
            'discSequence' => $discSequence
        ];
        $command = Cmd::create($data);

        $this->repoMap['PsvDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($licenceType)
            ->andReturn([])
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandDecreasing()
    {
        $this->setExpectedException(ValidationException::class);

        $licenceType = 'ltyp_r';
        $startNumber = 1;
        $discSequence = 2;
        $data = [
            'licenceType' => $licenceType,
            'startNumber' => $startNumber,
            'discSequence' => $discSequence
        ];
        $command = Cmd::create($data);

        $this->repoMap['PsvDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($licenceType)
            ->andReturn(['disc1'])
            ->once()
            ->getMock();

        $mockDiscSequence = m::mock()
            ->shouldReceive('getDiscNumber')
            ->with($licenceType)
            ->andReturn(2)
            ->once()
            ->getMock();

        $this->repoMap['DiscSequence']
            ->shouldReceive('fetchById')
            ->with($discSequence)
            ->andReturn($mockDiscSequence)
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $licenceType = 'ltyp_r';
        $startNumber = 1;
        $discSequence = 2;
        $data = [
            'licenceType' => $licenceType,
            'startNumber' => $startNumber,
            'discSequence' => $discSequence
        ];
        $command = Cmd::create($data);

        $mockDisc = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(3)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['PsvDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($licenceType)
            ->andReturn([$mockDisc])
            ->once()
            ->shouldReceive('setIsPrintingOn')
            ->with([$mockDisc])
            ->once()
            ->getMock();

        $mockDiscSequence = m::mock()
            ->shouldReceive('getDiscNumber')
            ->with($licenceType)
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->repoMap['DiscSequence']
            ->shouldReceive('fetchById')
            ->with($discSequence)
            ->andReturn($mockDiscSequence)
            ->once()
            ->getMock();

        $printDiscData = [
            'discs' => [$mockDisc],
            'type' => 'PSV',
            'startNumber' => $startNumber
        ];
        $this->expectedSideEffect(PrintDiscsCommand::class, $printDiscData, new Result());

        $createVehicleListData = [
            'id' => 3,
            'knownValues' => ['NO_DISCS_PRINTED' => ['count' => 1]]
        ];
        $this->expectedSideEffect(CreatePsvVehicleListForDiscsCommand::class, $createVehicleListData, new Result());

        $expected = [
            'id' => [],
            'messages' => [
                'PSV discs printed',
                'Vehicle list generated for licence 3'
            ]
        ];
        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }
}
