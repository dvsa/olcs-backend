<?php

/**
 * CancelFeeTestt
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCommand;

use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

/**
 * CancelFeeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CancelFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CancelFee();
        $this->mockRepo('Fee', Fee::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            FeeEntity::STATUS_CANCELLED
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = CancelFeeCommand::create(['id' => 863]);

        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setId(863);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($fee);
        $this->repoMap['Fee']->shouldReceive('save')->with($fee)->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Fee 863 cancelled successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->mapRefData(FeeEntity::STATUS_CANCELLED), $fee->getFeeStatus());
    }
}
