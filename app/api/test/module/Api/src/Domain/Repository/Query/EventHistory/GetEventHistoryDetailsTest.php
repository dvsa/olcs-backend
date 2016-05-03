<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Continuations;

use Dvsa\Olcs\Api\Domain\Repository\Query\EventHistory\GetEventHistoryDetails;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;

/**
 * Get event history details test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetEventHistoryDetailsTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [];
    protected $columnNameMap = [];

    public function paramProvider()
    {
        return [
            [
                [1, [1, 2]],
                ['version' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY],
                [1, [1, 2]],
                ['version' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            ]
        ];
    }

    protected function getSut()
    {
        $sut = new GetEventHistoryDetails();
        $sut->setHistoryTable('application_hist');
        return $sut;
    }

    protected function getExpectedQuery()
    {
        return 'SELECT * FROM application_hist WHERE id = :id AND version IN (:version) ORDER BY version
DESC LIMIT 2';
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecuteWithException($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->markTestSkipped('Not required for this test');
    }

}
