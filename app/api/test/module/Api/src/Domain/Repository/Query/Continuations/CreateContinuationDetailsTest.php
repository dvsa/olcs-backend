<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Continuations;

use Dvsa\Olcs\Api\Domain\Repository\Query\Continuations\CreateContinuationDetails;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;

/**
 * Create continuation details test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateContinuationDetailsTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        ContinuationDetail::class => 'continuation_detail'
    ];

    protected $columnNameMap = [
        ContinuationDetail::class => [
            'licence' => [
                'isAssociation' => true,
                'column' => 'licence_id'
            ],
            'received' => [
                'column' => 'received'
            ],
            'status' => [
                'column' => 'status'
            ],
            'continuation' => [
                'isAssociation' => true,
                'column' => 'continuation_id'
            ],
            'createdOn' => [
                'column' => 'created_on'
            ],
            'createdBy' => [
                'column' => 'created_by'
            ],
        ],
    ];

    public function paramProvider()
    {
        return [
            ['', '', '', '']
        ];
    }

    protected function getSut()
    {
        return new CreateContinuationDetails();
    }

    protected function getExpectedQuery()
    {
        return '';
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecuteWithException($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->markTestSkipped('Not required for this test');
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecute($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->markTestSkipped('Not required for this test');
    }

    public function testExecuteInsert()
    {
        $this->connection->shouldReceive('quote')
            ->times(8)
            ->andReturnUsing(
                function ($arg) {
                    $quoted = [
                        0 => "'0'",
                        1 => "'1'",
                        2 => "'2'",
                        'status' => "'status'"
                    ];
                    return $quoted[$arg];
                }
            );

        $this->connection->shouldReceive('executeUpdate')
            ->with(
                'INSERT INTO continuation_detail '
                . '(licence_id, received, status, continuation_id, created_on, created_by) '
                . 'VALUES (\'1\', \'0\', \'status\', \'2\', NOW(), :currentUserId), '
                . '(\'1\', \'0\', \'status\', \'2\', NOW(), :currentUserId)'
            )->once()
            ->andReturn('result');
        $this->assertEquals('result', $this->sut->executeInsert([1, 1], false, 'status', 2));
    }

    public function testExecuteInsertException()
    {
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

        $this->connection->shouldReceive('quote')
            ->times(4)
            ->andReturnUsing(
                function ($arg) {
                    $quoted = [
                        0 => "'0'",
                        1 => "'1'",
                        2 => "'2'",
                        'status' => "'status'"
                    ];
                    return $quoted[$arg];
                }
            );

        $this->connection->shouldReceive('executeUpdate')
            ->with(
                'INSERT INTO continuation_detail '
                . '(licence_id, received, status, continuation_id, created_on, created_by) '
                . 'VALUES (\'1\', \'0\', \'status\', \'2\', NOW(), :currentUserId)'
            )->once()
            ->andThrow(new \Exception());

        $this->sut->executeInsert([1], false, 'status', 2);
    }
}
