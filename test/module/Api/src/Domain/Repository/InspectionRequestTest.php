<?php

/**
 * Inspection Request test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Inspection\InspectionRequest;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\InspectionRequest as InspectionRequestRepo;
use Doctrine\ORM\EntityRepository;

/**
 * Inspection Request test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(InspectionRequestRepo::class);
    }

    public function testFetchForInspectionRequest()
    {
        $inspectionRequestId = 1;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getSingleResult')
            ->andReturn('RESULT');

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.licenceType', 'lt')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.organisation', 'l_o')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_o.organisationPersons', 'l_o_p')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_o_p.person', 'l_o_p_p')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_o.tradingNames', 'l_o_tn')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_o.licences', 'l_o_l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.workshops', 'l_w')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_w.contactDetails', 'l_w_cd')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_w_cd.address', 'l_w_cd_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.operatingCentres', 'l_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_oc.operatingCentre', 'l_oc_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.correspondenceCd', 'l_ccd')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_ccd.address', 'l_ccd_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_ccd.phoneContacts', 'l_ccd_pc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_ccd_pc.phoneContactType', 'l_ccd_pc_pct')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.tmLicences', 'l_tml')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_tml.transportManager', 'l_tml_tm')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_tml_tm.homeCd', 'l_tml_tm_hcd')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_tml_tm_hcd.person', 'l_tml_tm_hcd_p')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.enforcementArea', 'l_ea')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('operatingCentre', 'oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('oc.address', 'oc_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('application', 'a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a.licence', 'a_l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a.licenceType', 'a_lt')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a.operatingCentres', 'a_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a_oc.operatingCentre', 'a_oc_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a_oc_oc.address', 'a_oc_oc_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($inspectionRequestId)
            ->andReturnSelf()
            ->once()
            ->shouldReceive('withRefData')
            ->andReturnSelf()
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(InspectionRequest::class)
            ->andReturn($repo);

        $result = $this->sut->fetchForInspectionRequest($inspectionRequestId);
        $this->assertEquals('RESULT', $result);
    }
}
