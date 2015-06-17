<?php

/**
 * Licence test
 * 
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Doctrine\DBAL\LockMode;


/**
 * Licence test
 * 
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(LicenceRepo::class);
    }

    public function testGetSerialNoPrefixFromTrafficArea()
    {
        $licenceId = 1;
        $mockLicence = m::mock()
            ->shouldReceive('getTrafficArea')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $sut = m::mock(LicenceRepo::class)
            ->makePartial()
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->getMock();

        $this->assertEquals(CommunityLicEntity::PREFIX_NI, $sut->getSerialNoPrefixFromTrafficArea($licenceId));
    }

    public function testFetchSafetyDetailsUsingId()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(null);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('workshops', 'w')
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchSafetyDetailsUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchSafetyDetailsUsingIdWithResults()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        $result = m::mock(Licence::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('workshops', 'w')
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchSafetyDetailsUsingId($command, Query::HYDRATE_OBJECT, 1);
    }
}
