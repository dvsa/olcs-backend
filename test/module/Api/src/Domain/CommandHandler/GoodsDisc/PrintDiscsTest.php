<?php

/**
 * Print goods discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\GoodsDisc;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\GoodsDisc\PrintDiscs;
use Dvsa\Olcs\Api\Domain\Repository\DiscSequened as DiscSequenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc as GoodsDiscRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\GoodsDisc\PrintDiscs as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs as PrintDiscsCommand;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as CreateVehicleListDocumentCommand;

/**
 * Print goods discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrintDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintDiscs();
        $this->mockRepo('DiscSequence', DiscSequenceRepo::class);
        $this->mockRepo('GoodsDisc', GoodsDiscRepo::class);

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

        $this->repoMap['GoodsDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($niFlag, $licenceType)
            ->andReturn([])
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandDecreasing()
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

        $this->repoMap['GoodsDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($niFlag, $licenceType)
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

        $mockDisc = m::mock()
            ->shouldReceive('getLicenceVehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('getLicence')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(3)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['GoodsDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($niFlag, $licenceType)
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
            'type' => 'Goods',
            'startNumber' => $startNumber
        ];
        $this->expectedSideEffect(PrintDiscsCommand::class, $printDiscData, new Result());

        $createVehicleListData = [
            'id' => 3,
            'type' => 'dp'
        ];
        $this->expectedSideEffect(CreateVehicleListDocumentCommand::class, $createVehicleListData, new Result());

        $expected = [
            'id' => [],
            'messages' => [
                'Goods discs printed',
                'Vehicle list generated for licence 3'
            ]
        ];
        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }
}
