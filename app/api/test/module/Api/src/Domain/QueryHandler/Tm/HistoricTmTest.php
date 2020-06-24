<?php

/**
 * HistoricTm Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\Tm\HistoricTm as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Tm\HistoricTm as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\Repository\HistoricTm as HistoricTmRepo;
use Dvsa\Olcs\Api\Entity\Tm\HistoricTm as HistoricTmEntity;

/**
 * HistoricTm Query Handler Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class HistoricTmTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('HistoricTm', HistoricTmRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['historicId' => 1]);

        $mockList = [
            0 => [
                'id' => 3,
                'foreName' => 'John',
                'familyName' => 'Doe',
                'applicationId' => 3,
                'licNo' => 'AA333333',
                'seenContract' => false,
                'seenQualification' => true,
                'hoursPerWeek' => '33',
                'licOrApp' => 'L',
                'dateAdded' => '2003-03-01',
                'dateRemoved' => '2003-03-02'
            ],
            1 => [
                'id' => 5,
                'foreName' => 'John1',
                'familyName' => 'Doe1',
                'applicationId' => 5,
                'licNo' => 'AA555555',
                'seenContract' => true,
                'seenQualification' => false,
                'hoursPerWeek' => '35',
                'licOrApp' => 'A',
                'dateAdded' => '2005-05-01',
                'dateRemoved' => '2005-05-02'
            ],
            2 => [
                'id' => 5,
                'foreName' => 'John2',
                'familyName' => 'Doe2',
                'applicationId' => 5,
                'licNo' => 'AA555555',
                'seenContract' => true,
                'seenQualification' => false,
                'hoursPerWeek' => '35',
                'licOrApp' => 'A',
                'dateAdded' => '2005-05-01',
                'dateRemoved' => '2005-05-02'
            ]
        ];

        $mock = m::mock(HistoricTmEntity::class)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn($mockList[0])
            ->getMock();

        $this->repoMap['HistoricTm']
            ->shouldReceive('fetchList')
            ->with($query)
            ->once()
            ->andReturn($mockList)
            ->shouldReceive('fetchById')
            ->with($mockList[0]['id'])
            ->once()
            ->andReturn($mock);

        $result = $this->sut->handleQuery($query)->serialize();

        $this->assertCount(2, $result['applicationData']);
        $this->assertCount(1, $result['licenceData']);
        $this->assertEquals('John', $result['foreName']);
        $this->assertEquals('Doe', $result['familyName']);
    }
}
