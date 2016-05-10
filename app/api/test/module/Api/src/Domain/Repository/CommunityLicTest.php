<?php

/**
 * Community Lic test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

/**
 * Community Lic test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(CommunityLicRepo::class);
    }

    public function testFetchOfficeCopy()
    {
        $licenceId =1;
        $issueNo = 0;
        $mockQb = m::mock();
        $mockQb->shouldReceive('expr->eq')->with('m.licence', ':licence')->once()->andReturn('foo');
        $mockQb->shouldReceive('andWhere')->with('foo')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.issueNo', ':issueNo')->once()->andReturn('bar');
        $mockQb->shouldReceive('andWhere')->with('bar')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licence', $licenceId)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('issueNo', $issueNo)->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.status', ':pending')->once()->andReturn('pending');
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':active')->once()->andReturn('active');
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':withdrawn')->once()->andReturn('withdrawn');
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':suspended')->once()->andReturn('suspended');
        $mockQb->shouldReceive('setParameter')
            ->with('pending', CommunityLicEntity::STATUS_PENDING)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('active', CommunityLicEntity::STATUS_ACTIVE)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('withdrawn', CommunityLicEntity::STATUS_WITHDRAWN)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('suspended', CommunityLicEntity::STATUS_SUSPENDED)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('expr->orX')
            ->with('pending', 'active', 'withdrawn', 'suspended')
            ->once()
            ->andReturn('statuses');
        $mockQb->shouldReceive('andWhere')->with('statuses')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->getOneOrNullResult')
            ->once()
            ->andReturn(['result']);

        $this->assertEquals(['result'], $this->sut->fetchOfficeCopy($licenceId));
    }

    public function testFetchValidLicences()
    {
        $licenceId = 1;
        $issueNo = 0;
        $mockQb = m::mock();
        $mockQb->shouldReceive('expr->eq')->with('m.licence', ':licence')->once()->andReturn('foo');
        $mockQb->shouldReceive('andWhere')->with('foo')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->neq')->with('m.issueNo', ':issueNo')->once()->andReturn('bar');
        $mockQb->shouldReceive('andWhere')->with('bar')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.status', ':pending')->once()->andReturn('statuspending');
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':active')->once()->andReturn('statusactive');
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':withdrawn')->once()->andReturn('statuswithdrawn');
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':suspended')->once()->andReturn('statussuspended');
        $mockQb->shouldReceive('expr->orX')
            ->with(
                'statuspending',
                'statusactive',
                'statuswithdrawn',
                'statussuspended'
            )
            ->once()
            ->andReturn('statuses');
        $mockQb->shouldReceive('andWhere')->with('statuses')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licence', $licenceId)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('issueNo', $issueNo)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('pending', CommunityLicEntity::STATUS_PENDING)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('active', CommunityLicEntity::STATUS_ACTIVE)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('withdrawn', CommunityLicEntity::STATUS_WITHDRAWN)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('suspended', CommunityLicEntity::STATUS_SUSPENDED)->once()->andReturnSelf();
        $mockQb->shouldReceive('orderBy')->with('m.issueNo', 'ASC')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->execute')->once()->andReturn('result');
        $this->assertEquals('result', $this->sut->fetchValidLicences($licenceId));
    }

    public function testFetchLicencesById()
    {
        $mockQb = m::mock();
        $mockQb->shouldReceive('expr->eq')->with('m.id', ':id1')->once()->andReturn('id1');
        $mockQb->shouldReceive('orWhere')->with('id1')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('id1', 1)->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->execute')->once()->andReturn('result');
        $this->assertEquals('result', $this->sut->fetchLicencesByIds([1]));
    }

    public function testFetchActiveLicences()
    {
        $licenceId = 1;

        $mockQb = m::mock();
        $mockQb->shouldReceive('expr->eq')->with('m.licence', ':licence')->once()->andReturn('licence');
        $mockQb->shouldReceive('andWhere')->with('licence')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.status', ':status')->once()->andReturn('status');
        $mockQb->shouldReceive('andWhere')->with('status')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licence', $licenceId)->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('status', CommunityLicEntity::STATUS_ACTIVE)->once()->andReturnSelf();
        $mockQb->shouldReceive('orderBy')->with('m.issueNo', 'ASC')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->execute')->once()->andReturn('result');
        $this->assertEquals('result', $this->sut->fetchActiveLicences($licenceId));
    }

    public function testApplyListFilters()
    {
        // it's quite hard to test this protected method because of a lot of doctrine's
        // internal methods mocking required
        // so it's more reasonable to test this method in isolation
        $sut = m::mock(CommunityLicRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $licenceId = 1;
        $statuses = 'active';
        $conditions = [
            'm.status = :status0'
        ];

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getStatuses')
            ->andReturn($statuses)
            ->twice()
            ->shouldReceive('getLicence')
            ->andReturn($licenceId)
            ->twice()
            ->getMock();

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->orX->addMultiple')->with($conditions)->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('status0', 'active')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.licence', ':licence')->once()->andReturn('licence');
        $mockQb->shouldReceive('andWhere')->with('licence')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licence', $licenceId)->once()->andReturnSelf();

        $sut->applyListFilters($mockQb, $mockQuery);
    }

    public function testExpireAllForLicence()
    {
        $licenceId = 123;

        $this->expectQueryWithData('CommunityLicence\ExpireAllForLicence', ['licence' => 123, 'status' => 'foo']);

        $this->sut->expireAllForLicence($licenceId, 'foo');
    }

    public function testExpireAllForLicenceNoStatus()
    {
        $licenceId = 123;

        $this->expectQueryWithData('CommunityLicence\ExpireAllForLicence', ['licence' => 123]);

        $this->sut->expireAllForLicence($licenceId);
    }
}
