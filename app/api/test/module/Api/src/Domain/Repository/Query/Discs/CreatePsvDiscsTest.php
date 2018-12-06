<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\Discs\CreatePsvDiscs;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;

/**
 * CreatePsvDiscsTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreatePsvDiscsTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        PsvDisc::class => 'psv_disc'
    ];

    protected $columnNameMap = [
        PsvDisc::class => [
            'licence' => [
                'isAssociation' => true,
                'column' => 'licence_id'
            ],
            'isCopy' => [
                'column' => 'is_copy'
            ],
            'createdOn' => [
                'column' => 'created_on'
            ],
            'createdBy' => [
                'column' => 'created_by'
            ],
            'lastModifiedOn' => [
                'column' => 'last_modified_on'
            ],
            'lastModifiedBy' => [
                'column' => 'last_modified_by'
            ],
        ],
    ];

    public function paramProvider()
    {
        $today = new DateTime();

        return [
            [
                [],
                [],
                [
                    'ceasedDate' => $today->format('Y-m-d H:i:s')
                ],
                []
            ]
        ];
    }

    protected function getSut()
    {
        return new CreatePsvDiscs();
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

    /**
     * @dataProvider paramProvider
     */
    public function testExecuteAsSystemUser($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->markTestSkipped('Not required for this test');
    }

    public function testExecuteInsert()
    {
        $this->connection->shouldReceive('quote')->with(1102)->times(4)->andReturn("'1102'");
        $this->connection->shouldReceive('quote')->with(0)->times(4)->andReturn("'0'");

        $sql =
            'INSERT INTO psv_disc (licence_id, is_copy, created_on, created_by, last_modified_on, last_modified_by) '.
            'VALUES (\'1102\', \'0\', NOW(), 1, NOW(), 1), (\'1102\', \'0\', NOW(), 1, NOW(), 1), '.
            '(\'1102\', \'0\', NOW(), 1, NOW(), 1), (\'1102\', \'0\', NOW(), 1, NOW(), 1)';

        $this->connection->shouldReceive('executeUpdate')
            ->with($sql)
            ->once()
            ->andReturn('result');

        $this->assertEquals('result', $this->sut->executeInsert(1102, 4, false));
    }

    public function testExecuteZeroDiscs()
    {
        $this->assertEquals(0, $this->sut->executeInsert(1102, 0, false));
    }

    public function testExecuteInsertException()
    {
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

        $this->connection->shouldReceive('quote')->with(1102)->times(4)->andReturn("'1102'");
        $this->connection->shouldReceive('quote')->with(0)->times(4)->andReturn("'0'");

        $sql =
            'INSERT INTO psv_disc (licence_id, is_copy, created_on, created_by, last_modified_on, last_modified_by) '.
            'VALUES (\'1102\', \'0\', NOW(), 1, NOW(), 1), (\'1102\', \'0\', NOW(), 1, NOW(), 1), '.
            '(\'1102\', \'0\', NOW(), 1, NOW(), 1), (\'1102\', \'0\', NOW(), 1, NOW(), 1)';

        $this->connection->shouldReceive('executeUpdate')
            ->with($sql)
            ->once()
            ->andThrow(new \Exception());

        $this->assertEquals('result', $this->sut->executeInsert(1102, 4, false));
    }
}
