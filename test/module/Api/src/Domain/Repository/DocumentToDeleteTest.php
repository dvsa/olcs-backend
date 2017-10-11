<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\DocumentToDelete;

/**
 * DocumentToDeleteTest
 */
class DocumentToDeleteTest extends RepositoryTestCase
{
    /**
     * @var DocumentToDelete
     */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(DocumentToDelete::class);
    }

    public function testFetchListOfDocumentToDelete()
    {
        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);
        $this->mockCreateQueryBuilder($mockQb);

        $mockQb->shouldReceive('setMaxResults')->with(77)->once();
        $mockQb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['FOO']);

        $this->assertSame(['FOO'], $this->sut->fetchListOfDocumentToDelete(77));

    }
}
