<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Cli\Domain\CommandHandler\SetViFlags;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Mockery as m;

/**
 * Create VI Extract Files Test
 */
class SetViFlagsTest extends CommandHandlerTestCase
{
    /**
     * @var m\Mock;
     */
    private $mockDbConnection;

    public function setUp()
    {
        $this->sut = new SetViFlags();

        $this->mockDbConnection = m::mock(Connection::class);
        $this->mockedSmServices['doctrine.connection.ormdefault'] = $this->mockDbConnection;

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $mockStmt = m::mock();
        $mockStmt->shouldReceive('execute')->with()->once()->andReturn(true);
        $mockStmt->shouldReceive('fetchAll')->with()->once()->andReturn([0 => ['sp' => 'Done something']]);
        $this->mockDbConnection->shouldReceive('prepare')->with('CALL vi_set_flags()')->once()->andReturn($mockStmt);

        $response = $this->sut->handleCommand(\Dvsa\Olcs\Cli\Domain\Command\SetViFlags::create([]));

        $this->assertEquals(['id' => [], 'messages' => ['VI Flags set']], $response->toArray());
    }

    public function testHandleCommandError()
    {
        $mockStmt = m::mock();
        $mockStmt->shouldReceive('execute')->with()->once()->andReturn(true);
        $mockStmt->shouldReceive('fetchAll')->with()->once()->andReturn([0 => ['Result' => 'Something gone wrong']]);
        $this->mockDbConnection->shouldReceive('prepare')->with('CALL vi_set_flags()')->once()->andReturn($mockStmt);

        $this->expectException(RuntimeException::class);

        $this->sut->handleCommand(\Dvsa\Olcs\Cli\Domain\Command\SetViFlags::create([]));
    }
}
