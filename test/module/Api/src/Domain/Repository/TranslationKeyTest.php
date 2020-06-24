<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKey as Repo;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\TranslationKey\GetList;

/**
 * TranslationKeyTest
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class TranslationKeyTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(Repo::class);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslatedText')
            ->andReturn('transText')
            ->twice();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('select')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('m.translationKeyTexts', 'tkt')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('tkt.translatedText LIKE :translatedText')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('translatedText', '%transText%')
            ->once()
            ->andReturnSelf();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersNullSearch()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslatedText')
            ->andReturn(null)
            ->once();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersNotGetList()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(QueryInterface::class);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $this->assertNull( $this->sut->applyListFilters($qb, $query) );
    }
}
