<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitRange;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange\Delete as DeleteHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as PermitRangeRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitRange\Delete as DeleteCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as PermitRangeEntity;

/**
 * Create IRHP Permit Range Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class DeleteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteHandler;
        $this->mockRepo('IrhpPermitRange', PermitRangeRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'id' => '1'
        ];

        $command = DeleteCmd::create($cmdData);

        $id = $cmdData['id'];

        $irhpPermitRange = m::mock(PermitRangeEntity::class);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('fetchById')
            ->with($id)
            ->once()
            ->andReturn($irhpPermitRange);

        $irhpPermitRange->shouldReceive('canDelete')->once()->andReturn(true);


        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('delete')
            ->once()
            ->with($irhpPermitRange);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['id' => 1],
            'messages' => ['Permit Range Deleted']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     *
     * Test for preventing a Permit Range being deleted if it has existing dependencies - no values are asserted as
     * this tests to ensure that a validation exception is thrown.
     */
    public function testHandleCantDelete()
    {
        $cmdData = [
            'id' => '1'
        ];

        $command = DeleteCmd::create($cmdData);

        $id = $cmdData['id'];

        $irhpPermitRange = m::mock(PermitRangeEntity::class);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('fetchById')
            ->with($id)
            ->once()
            ->andReturn($irhpPermitRange);

        $irhpPermitRange->shouldReceive('canDelete')->once()->andReturn(false);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('delete')
            ->never();
        $this->sut->handleCommand($command);
    }
}
