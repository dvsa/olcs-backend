<?php

/**
 * Confirm printing
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\GoodsDisc;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\GoodsDisc\ConfirmPrinting;
use Dvsa\Olcs\Api\Domain\Repository\DiscSequened as DiscSequenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc as GoodsDiscRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\GoodsDisc\ConfirmPrinting as Cmd;

/**
 * Confirm printing
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ConfirmPrintingTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ConfirmPrinting();
        $this->mockRepo('DiscSequence', DiscSequenceRepo::class);
        $this->mockRepo('GoodsDisc', GoodsDiscRepo::class);

        parent::setUp();
    }

    public function testHandleCommandWithAssigningNumber()
    {
        $niFlag = 'N';
        $licenceType = 'ltyp_r';
        $startNumber = 1;
        $endNumber = 5;
        $discSequence = 2;
        $data = [
            'niFlag' => $niFlag,
            'licenceType' => $licenceType,
            'startNumber' => $startNumber,
            'discSequence' => $discSequence,
            'endNumber' => $endNumber,
            'isSuccessfull' => true
        ];
        $command = Cmd::create($data);

        $this->repoMap['GoodsDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($niFlag, $licenceType)
            ->andReturn('discs')
            ->once()
            ->shouldReceive('setIsPrintingOffAndAssignNumbers')
            ->with('discs', $startNumber)
            ->getMock();

        $mockDiscSequence = m::mock()
            ->shouldReceive('setDiscStartNumber')
            ->with($licenceType, $endNumber + 1)
            ->once()
            ->getMock();

        $this->repoMap['DiscSequence']
            ->shouldReceive('fetchById')
            ->with($discSequence)
            ->andReturn($mockDiscSequence)
            ->shouldReceive('save')
            ->with($mockDiscSequence)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);
        $expected = [
            'id' => [],
            'messages' => [
                'Printing flag is now off and numbers assigned to discs'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithSetPrintingOff()
    {
        $niFlag = 'N';
        $licenceType = 'ltyp_r';
        $startNumber = 1;
        $endNumber = 5;
        $discSequence = 2;
        $data = [
            'niFlag' => $niFlag,
            'licenceType' => $licenceType,
            'startNumber' => $startNumber,
            'discSequence' => $discSequence,
            'endNumber' => $endNumber,
            'isSuccessfull' => false
        ];
        $command = Cmd::create($data);

        $this->repoMap['GoodsDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($niFlag, $licenceType)
            ->andReturn('discs')
            ->once()
            ->shouldReceive('setIsPrintingOff')
            ->with('discs')
            ->getMock();

        $result = $this->sut->handleCommand($command);
        $expected = [
            'id' => [],
            'messages' => [
                'Printing flag is now off'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
