<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use Dvsa\Olcs\Cli\Domain\CommandHandler\SetViFlags;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
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

    public function setUp(): void
    {
        $this->sut = new SetViFlags();

        $this->mockDbConnection = m::mock(Connection::class);
        $this->mockedSmServices['doctrine.connection.ormdefault'] = $this->mockDbConnection;

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $mockStmt = m::mock(Statement::class);
        $mockStmt->expects('executeQuery')->withNoArgs()->andReturn(m::mock(Result::class));
        $this->mockDbConnection->shouldReceive('prepare')->with('CALL vi_set_flags()')->once()->andReturn($mockStmt);

        $response = $this->sut->handleCommand(\Dvsa\Olcs\Cli\Domain\Command\SetViFlags::create([]));

        $this->assertEquals(['id' => [], 'messages' => ['VI Flags set']], $response->toArray());
    }
}
