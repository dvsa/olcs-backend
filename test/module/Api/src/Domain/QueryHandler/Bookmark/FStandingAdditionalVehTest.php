<?php

/**
 * FStandingAdditionalVeh Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bookmark;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark\FStandingAdditionalVeh;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\FinancialStandingRate as Repo;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\FStandingAdditionalVeh as Qry;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate as Entity;

/**
 * FStandingAdditionalVeh Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FStandingAdditionalVehTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new FStandingAdditionalVeh();
        $this->mockRepo('FinancialStandingRate', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(
            [
                'goodsOrPsv' => 'Goods',
                'licenceType' => 'Standard',
                'effectiveFrom' => '2015-01-01'
            ]
        );

        /** @var Entity $entity */
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('serialize')
            ->with([])
            ->andReturn(['id' => 111]);

        $this->repoMap['FinancialStandingRate']->shouldReceive('fetchLatestRateForBookmark')
            ->with('Goods', 'Standard', '2015-01-01')
            ->andReturn([$entity]);

        $this->assertEquals(['Results' => [['id' => 111]]], $this->sut->handleQuery($query));
    }
}
