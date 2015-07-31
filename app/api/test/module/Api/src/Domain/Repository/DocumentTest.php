<?php

/**
 * Document test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;

/**
 * Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
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

    /**
     * @dataProvider tmProvider
     */
    public function testFetchListForTmApplication($type)
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
            ->with('subCategory', CategoryEntity::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.transportManager', ':transportManager')->once()->andReturn('tm');
        $mockQb->shouldReceive('andWhere')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('transportManager', 1)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.' . $type, ':' . $type)->once()->andReturn('tm');
        $mockQb->shouldReceive('andWhere')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with($type, 2)
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

        if ($type == 'licence') {
            $this->assertEquals('result', $sut->fetchListForTmLicence(1, 2));
        } else {
            $this->assertEquals('result', $sut->fetchListForTmApplication(1, 2));
        }
    }

    public function tmProvider()
    {
        return [
            ['licence'],
            ['application'],
        ];
    }

    public function testFetchUnlinkedOcDocumentsForEntity()
    {
        $category = m::mock(Category::class)->makePartial();
        $subCategory = m::mock(SubCategory::class)->makePartial();

        $this->em->shouldReceive('getReference')
            ->with(Category::class, Category::CATEGORY_APPLICATION)
            ->andReturn($category);

        $this->em->shouldReceive('getReference')
            ->with(SubCategory::class, Category::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL)
            ->andReturn($subCategory);

        /** @var Document $doc1 */
        $doc1 = m::mock(Document::class)->makePartial();

        /** @var Document $doc2 */
        $doc2 = m::mock(Document::class)->makePartial();
        $doc2->setCategory($category);
        $doc2->setSubCategory($subCategory);

        $docs = new ArrayCollection();
        $docs->add($doc1);
        $docs->add($doc2);

        /** @var Application $entity */
        $entity = m::mock(Application::class)->makePartial();
        $entity->setDocuments($docs);

        $collection = $this->sut->fetchUnlinkedOcDocumentsForEntity($entity);

        $this->assertEquals(1, $collection->count());
        $this->assertEquals($doc2, $collection->first());
    }
}
