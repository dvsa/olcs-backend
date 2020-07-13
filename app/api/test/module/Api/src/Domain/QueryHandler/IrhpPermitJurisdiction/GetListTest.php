<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermitJurisdiction;

use Doctrine\Common\Collections\ArrayCollection;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitJurisdiction\GetList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitJurisdictionQuota as PermitJurisdictionQuotaRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitJurisdictionQuota as PermitJurisdictionQuotaEntity;
use Dvsa\Olcs\Transfer\Query\IrhpPermitJurisdiction\GetList as ListQuery;

/**
 * GetList Test
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('IrhpPermitJurisdictionQuota', PermitJurisdictionQuotaRepo::class);

        parent::setUp();
    }

    // Test Handle Command
    public function testHandleCommand()
    {
        $query = ListQuery::create([ 'irhpPermitStock' => '1']);

        $item1 = m::mock(PermitJurisdictionQuotaEntity::class)->makePartial();
        $item2 = m::mock(PermitJurisdictionQuotaEntity::class)->makePartial();
        $permitJurisdictions = new ArrayCollection();

        $permitJurisdictions->add($item1);
        $permitJurisdictions->add($item2);

        $this->repoMap['IrhpPermitJurisdictionQuota']
            ->shouldReceive('fetchByIrhpPermitStockId')
            ->with($query->getIrhpPermitStock())
            ->once()
            ->andReturn($permitJurisdictions);


        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                0 => [],
                1 => []
            ]
        ];

        Assert::assertArraySubset($expected, $result);
    }
}
