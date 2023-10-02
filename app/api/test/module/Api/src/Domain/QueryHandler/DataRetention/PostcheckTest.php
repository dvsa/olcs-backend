<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\DataRetention;

use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Query\DataRetention\Postcheck as Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention\Postcheck;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class PostcheckTest extends QueryHandlerTestCase
{
    private $mockedConnection;

    public function setUp(): void
    {
        $this->sut = new Postcheck();
        $this->mockedConnection = m::mock(ServerInfoAwareConnection::class);
        $this->mockedSmServices['DoctrineOrmEntityManager'] = m::mock(EntityManager::class);
        $this->mockedSmServices['DoctrineOrmEntityManager']
            ->shouldReceive('getConnection->getNativeConnection')
            ->withNoArgs()
            ->andReturn($this->mockedConnection);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $mockResult = m::mock(Result::class);
        $mockResult->expects('fetchAllAssociative')
            ->withNoArgs()
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
            );

        $mockResult->expects('free')->withNoArgs();

        $mockStatement = m::mock(Statement::class);
        $mockStatement
            ->expects('executeQuery')
            ->withNoArgs()
            ->andReturn($mockResult);

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
