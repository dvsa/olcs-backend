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

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('m')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->execute')->once()->andReturn(['result']);

        $this->assertEquals('result', $this->sut->fetchOfficeCopy($licenceId));
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
}
