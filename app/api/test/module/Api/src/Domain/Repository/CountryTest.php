<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Mockery as m;

/**
 * Country test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CountryTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Country::class);
    }

    public function testFetchIdsAndDescriptions()
    {
        $idsAndDescriptions = [
            [
                'countryId' => 'AU',
                'description' => 'Austria'
            ],
            [
                'countryId' => 'RU',
                'description' => 'Russia'
            ],
        ];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('c.id as countryId, c.countryDesc as description')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(CountryEntity::class, 'c')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($idsAndDescriptions);

        $this->assertEquals(
            $idsAndDescriptions,
            $this->sut->fetchIdsAndDescriptions()
        );
    }
}
