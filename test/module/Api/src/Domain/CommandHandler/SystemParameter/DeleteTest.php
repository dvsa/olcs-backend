<?php

/**
 * Delete SystemParameter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\SystemParameter;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter\Delete as Delete;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\SystemParameter\DeleteSystemParameter as Cmd;

/**
 * Delete SystemParameter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DeleteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Delete();
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);

        parent::setUp();
    }

    public function testHandleCommandNeedReassign()
    {
        $command = Cmd::create(['id' => 1]);

        $mockSystemParameter = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->repoMap['SystemParameter']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($mockSystemParameter)
            ->shouldReceive('delete')
            ->with($mockSystemParameter)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['systemParameter' => 1],
            'messages' => ['System Parameter deleted successfully']
        ];
        $this->assertEquals($expected, $result->toArray());
    }
}
