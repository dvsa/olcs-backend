<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\DataRetention;

use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention\Precheck;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Class PrecheckTest
 */
class PrecheckTest extends CommandHandlerTestCase
{
    private $mockedConnection;

    public function setUp(): void
    {
        $this->sut = new Precheck();
        $this->mockedConnection = m::mock(ServerInfoAwareConnection::class);
        $this->mockedSmServices['DoctrineOrmEntityManager'] = m::mock(EntityManager::class);
        $this->mockedSmServices['DoctrineOrmEntityManager']
            ->shouldReceive('getConnection->getNativeConnection')
            ->andReturn($this->mockedConnection);
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getLimit')
            ->withNoArgs()
            ->andReturn(10);

        $mockStatement = m::mock(Statement::class);
        $mockStatement
            ->expects('execute')
            ->withNoArgs();

        $this->mockedConnection->shouldReceive('prepare')->with("CALL sp_dr_precheck(10);")->once()->andReturn($mockStatement);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Calling stored procedure sp_dr_precheck(10)',
                'Precheck procedure executed.'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
