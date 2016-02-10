<?php

/**
 * Confirm printing
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PsvDisc;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\PsvDisc\ConfirmPrinting;
use Dvsa\Olcs\Api\Domain\Repository\DiscSequened as DiscSequenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\PsvDisc as PsvDiscRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\PsvDisc\ConfirmPrinting as Cmd;

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
        $this->mockRepo('PsvDisc', PsvDiscRepo::class);

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

        $this->repoMap['PsvDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($licenceType)
            ->andReturn([['id' => 23, 'foo' => 'bar']])
            ->once()
            ->shouldReceive('setIsPrintingOffAndAssignNumbers')
            ->with([23], $startNumber)
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

        $this->repoMap['PsvDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($licenceType)
            ->andReturn([['id' => 23, 'foo' => 'bar']])
            ->once()
            ->shouldReceive('setIsPrintingOff')
            ->with([23])
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
