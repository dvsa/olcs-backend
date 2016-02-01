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
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as CreateVehicleListDocumentCommand;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreatQueue;

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

        $disc = ['id' => 12, 'licenceVehicle' => ['licence' => ['id' => 3]]];

        $this->repoMap['GoodsDisc']
            ->shouldReceive('fetchDiscsToPrint')
            ->with($niFlag, $licenceType)
            ->andReturn([$disc])
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

        $options = [
            'discs' => [12],
            'type' => 'Goods',
            'startNumber' => $startNumber
        ];
        $params = [
            'type' => Queue::TYPE_DISC_PRINTING,
            'status' => Queue::STATUS_QUEUED,
            'options' => json_encode($options)
        ];
        $this->expectedSideEffect(CreatQueue::class, $params, new Result());

        $licences = [
            3 => [
                'id' => 3,
                'type' => 'dp'
            ]
        ];
        $options = [
            'licences' => $licences
        ];
        $params = [
            'type' => Queue::TYPE_CREATE_GOODS_VEHICLE_LIST,
            'status' => Queue::STATUS_QUEUED,
            'options' => json_encode($options)
        ];
        $this->expectedSideEffect(CreatQueue::class, $params, new Result());

        $expected = [
            'id' => [],
            'messages' => [
                'Goods discs printed'
            ]
        ];
        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }
}
