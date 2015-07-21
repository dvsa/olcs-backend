<?php

/**
 * Document test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;

/**
 * Document test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DocumentTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(DocumentRepo::class);
    }

    public function testFetchListForTm()
    {
        $sut = m::mock(DocumentRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $mockQb->shouldReceive('expr->eq')->with('m.category', ':category')->once()->andReturn('category');
        $mockQb->shouldReceive('andWhere')->with('category')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('category', CategoryEntity::CATEGORY_TRANSPORT_MANAGER)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.subCategory', ':subCategory')->once()->andReturn('subCategory');
        $mockQb->shouldReceive('andWhere')->with('subCategory')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('subCategory', CategoryEntity::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.transportManager', ':transportManager')->once()->andReturn('tm');
        $mockQb->shouldReceive('andWhere')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('transportManager', 1)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('orderBy')->with('m.id', 'DESC')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                ->shouldReceive('execute')
                ->andReturn('result')
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $sut->shouldReceive('createQueryBuilder')
            ->andReturn($mockQb)
            ->once()
            ->getMock();

        $this->assertEquals('result', $sut->fetchListForTm(1));
    }
}
