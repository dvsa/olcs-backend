<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\DataRetention;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Query\DataRetention\Postcheck as Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention\Postcheck;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use PDO;

/**
 * Postcheck Test
 */
class PostcheckTest extends QueryHandlerTestCase
{
    private $mockedConnection;

    public function setUp(): void
    {
        $this->sut = new Postcheck();
        $this->mockedConnection = m::mock(Connection::class);
        $this->mockedSmServices['DoctrineOrmEntityManager'] = m::mock(EntityManager::class);
        $this->mockedSmServices['DoctrineOrmEntityManager']
            ->shouldReceive('getConnection->getWrappedConnection')
            ->withNoArgs()
            ->andReturn($this->mockedConnection);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $mockStatement = m::mock(Statement::class);
        $mockStatement
            ->shouldReceive('execute')
            ->once()
            ->withNoArgs()
            ->andReturn()
            ->ordered()
            ->shouldReceive('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->andReturn(
                [
                    [
                        'type' => 'rowcount',
                        'tableName' => 'queue',
                        'data1' => '28',
                        'data2' => '27'
                    ],
                    [
                        'type' => 'rowcount',
                        'tableName' => 'answer',
                        'data1' => '30',
                        'data2' => '29'
                    ]
                ]
            )
            ->ordered()
            ->shouldReceive('closeCursor')
            ->once()
            ->withNoArgs()
            ->andReturn()
            ->ordered();

        $this->mockedConnection->shouldReceive('prepare')->with("CALL sp_dr_postcheck();")->once()->andReturn($mockStatement);
        $query = Query::create([]);

        $result = $this->sut->handleQuery($query);

        $expected = [
            [
                'type' => 'rowcount',
                'tableName' => 'queue',
                'data1' => '28',
                'data2' => '27'
            ],
            [
                'type' => 'rowcount',
                'tableName' => 'answer',
                'data1' => '30',
                'data2' => '29'
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}
