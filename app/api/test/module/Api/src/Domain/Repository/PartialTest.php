<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Partial as Repo;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\PartialMarkup\GetList;

/**
 * PartialTest
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class PartialTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(Repo::class);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslationSearch')
            ->andReturn('searchText')
            ->times(2)
            ->shouldReceive('getCategory')
            ->times(3)
            ->andReturn(4)
            ->shouldReceive('getSubCategory')
            ->times(2)
            ->andReturn(33);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('orWhere')
            ->with('m.partialKey LIKE :translationSearch')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orWhere')
            ->with('m.description LIKE :translationSearch')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orWhere')
            ->with('m.prefix LIKE :translationSearch')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('leftJoin')
            ->with('m.partialMarkups', 'pms')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orWhere')
            ->with('pms.markup LIKE :translationSearch')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('leftJoin')
            ->with('m.partialTagLinks', 'ptl')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('leftJoin')
            ->with('ptl.tag', 'tag')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orWhere')
            ->with('tag.tag LIKE :translationSearch')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('leftJoin')
            ->with('m.partialCategoryLinks', 'pcl')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('translationSearch', '%searchText%')
            ->once()
            ->shouldReceive('andWhere')
            ->with('pcl.category = :category')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('pcl.subCategory = :subCategory')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('category', 4)
            ->once()
            ->andReturnSelf()->shouldReceive('setParameter')
            ->with('subCategory', 33)
            ->once()
            ->andReturnSelf();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersNullSearch()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslationSearch')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getCategory')
            ->once()
            ->andReturnNull()
            ->shouldReceive('getSubCategory')
            ->once()
            ->andReturnNull();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->sut->applyListFilters($qb, $query);
    }


    public function testApplyListFiltersCategory()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslationSearch')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getCategory')
            ->times(3)
            ->andReturn(4)
            ->shouldReceive('getSubCategory')
            ->once()
            ->andReturn(null);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('leftJoin')
            ->with('m.partialCategoryLinks', 'pcl')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('pcl.category = :category')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('category', 4)
            ->once()
            ->andReturnSelf();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersSubCategory()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslationSearch')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getCategory')
            ->times(2)
            ->andReturn(null)
            ->shouldReceive('getSubCategory')
            ->times(3)
            ->andReturn(44);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('leftJoin')
            ->with('m.partialCategoryLinks', 'pcl')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('pcl.subCategory = :subCategory')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('subCategory', 44)
            ->once()
            ->andReturnSelf();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersBothCategories()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(GetList::class);
        $query->shouldReceive('getTranslationSearch')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getCategory')
            ->times(3)
            ->andReturn(4)
            ->shouldReceive('getSubCategory')
            ->times(2)
            ->andReturn(33);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('leftJoin')
            ->with('m.partialCategoryLinks', 'pcl')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('pcl.category = :category')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('pcl.subCategory = :subCategory')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('category', 4)
            ->once()
            ->andReturnSelf()->shouldReceive('setParameter')
            ->with('subCategory', 33)
            ->once()
            ->andReturnSelf();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testApplyListFiltersNotGetList()
    {
        $this->setUpSut(Repo::class, true);

        $query = m::mock(QueryInterface::class);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $this->assertNull($this->sut->applyListFilters($qb, $query));
    }
}
