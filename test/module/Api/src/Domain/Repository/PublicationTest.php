<?php

/**
 * Publication test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Doctrine\ORM\EntityRepository;

/**
 * Publication test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(PublicationRepo::class);
    }

    /**
     * @param $qb
     * @return m\MockInterface
     */
    public function getMockRepo($qb)
    {
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        return $repo;
    }

    /**
     * Tests fetch latest publication for traffic area and type
     */
    public function testFetchLatestForTrafficAreaAndType()
    {
        $trafficArea = 'M';
        $pubType = 'A&D';
        $results = [0 => m::mock(PublicationEntity::class)];

        $mockQb = $this->getMockTaAndTypeQb($trafficArea, $pubType, $results);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationEntity::class)
            ->andReturn($repo);

        $this->sut->fetchLatestForTrafficAreaAndType($trafficArea, $pubType);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function testFetchLatestForTrafficAreaAndTypeNotFound()
    {
        $trafficArea = 'M';
        $pubType = 'A&D';
        $results = [];

        $mockQb = $this->getMockTaAndTypeQb($trafficArea, $pubType, $results);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationEntity::class)
            ->andReturn($repo);

        $this->sut->fetchLatestForTrafficAreaAndType($trafficArea, $pubType);
    }

    /**
     * @param string $trafficArea
     * @param string $pubType
     * @param array $results
     * @return m\MockInterface
     */
    public function getMockTaAndTypeQb($trafficArea, $pubType, $results)
    {
        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.trafficArea', ':trafficArea')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('trafficArea', $trafficArea)->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.pubType', ':pubType')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('pubType', $pubType)->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.pubStatus', ':pubStatus')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('pubStatus', PublicationEntity::PUB_NEW_STATUS)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)->andReturnSelf();

        return $mockQb;
    }

    public function testFetchPendingList()
    {
        $results = [0 => m::mock(PublicationEntity::class)];

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->in')->with('m.pubStatus', ':pubStatus')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('pubStatus', [PublicationEntity::PUB_NEW_STATUS, PublicationEntity::PUB_GENERATED_STATUS])
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationEntity::class)
            ->andReturn($repo);

        $this->sut->fetchPendingList();
    }
}
