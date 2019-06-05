<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use DateTime;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath as ApplicationPathEntity;
use Mockery as m;

/**
 * Application path test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationPathTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(ApplicationPath::class);
    }

    public function testFetchByApplicationPathIdAndSlug()
    {
        $irhpPermitTypeId = 4;
        $dateTime = m::mock(DateTime::class);
        $applicationPathEntity = m::mock(ApplicationPathEntity::class);

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $lessThanExpression = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr->lte')
            ->with('ap.effectiveFrom', ':now')
            ->once()
            ->andReturn($lessThanExpression);

        $queryBuilder->shouldReceive('select')
            ->with('ap')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(ApplicationPathEntity::class, 'ap')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ap.irhpPermitType) = :type')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with($lessThanExpression)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ap.effectiveFrom', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('type', $irhpPermitTypeId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('now', $dateTime)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleResult')
            ->once()
            ->andReturn($applicationPathEntity);

        $this->assertSame(
            $applicationPathEntity,
            $this->sut->fetchByIrhpPermitTypeIdAndDate($irhpPermitTypeId, $dateTime)
        );
    }

    public function testFetchByApplicationPathIdAndSlugNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Unable to locate application path');

        $irhpPermitTypeId = 4;
        $dateTime = m::mock(DateTime::class);

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $lessThanExpression = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr->lte')
            ->with('ap.effectiveFrom', ':now')
            ->once()
            ->andReturn($lessThanExpression);

        $queryBuilder->shouldReceive('select')
            ->with('ap')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(ApplicationPathEntity::class, 'ap')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ap.irhpPermitType) = :type')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with($lessThanExpression)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ap.effectiveFrom', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('type', $irhpPermitTypeId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('now', $dateTime)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleResult')
            ->once()
            ->andThrow(new NoResultException());

        $this->sut->fetchByIrhpPermitTypeIdAndDate($irhpPermitTypeId, $dateTime);
    }
}
