<?php

/**
 * EcmtCountriesList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\EcmtCountriesList as QueryHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtCountriesList as Qry;
use Mockery as m;
use Doctrine\ORM\Query;

/**
 * EcmtCountriesListTest
 */

class EcmtCountriesListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Country', \Dvsa\Olcs\Api\Domain\Repository\Country::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['isEcmtState' => 1]);

        $resultCount = 2;

        $mockEcmtCountry = m::mock(CountryEntity::class);
        $mockEcmtCountry->shouldReceive('serialize')->once();

        $this->repoMap['Country']->shouldReceive('fetchList')
            ->with(m::type(Qry::class), Query::HYDRATE_OBJECT)
            ->andReturn([$mockEcmtCountry]);

        $this->repoMap['Country']->shouldReceive('fetchCount')
            ->with(m::type(Qry::class))
            ->andReturn($resultCount);

        $result = $this->sut->handleQuery($query);
        $this->assertCount(2, $result);
        $this->assertEquals($resultCount, $result['count']);

        $fetchListVars = $this->sut->getListDto()->getArrayCopy();

        $this->assertEquals(1, $fetchListVars['isEcmtState']);
    }
}
