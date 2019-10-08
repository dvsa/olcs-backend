<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetList;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetListByIrhpApplication;
use Dvsa\Olcs\Transfer\Query\Permits\UnpaidEcmtPermits;

/**
 * IRHP Candidate Permit test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpCandidatePermitTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpCandidatePermit::class);
    }

    public function testFetchListForUnpaidEcmtPermits()
    {
        $id = 10;
        $status = RefData::PERMIT_APP_STATUS_AWAITING_FEE;

        $this->setUpSut(IrhpCandidatePermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.ecmtPermitApplication', 'epa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpApplication', 'ia')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf();

        $query = UnpaidEcmtPermits::create(
            [
                'id' => $id,
                'page' => 1,
                'limit' => 25,
                'status' => $status,
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND m.successful = [[true]] '
            . 'AND epa.status = [['.$status.']] '
            . 'AND ipa.ecmtPermitApplication = [['.$id.']]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForGetListByIrhpApplication()
    {
        $irhpApplicationId = 10;

        $this->setUpSut(IrhpCandidatePermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.ecmtPermitApplication', 'epa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpApplication', 'ia')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf()
            ->shouldReceive('order')->once()->andReturnSelf();

        $query = GetListByIrhpApplication::create(
            [
                'irhpApplication' => $irhpApplicationId,
                'page' => 1,
                'limit' => 25,
                'order' => 'id',
                'sort' => 'ASC',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND m.successful = [[true]] '
            . 'AND ia.status = [['.RefData::PERMIT_APP_STATUS_AWAITING_FEE.']] '
            . 'AND ipa.irhpApplication = [['.$irhpApplicationId.']]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListWithEcmtPermitApplication()
    {
        $ecmtPermitApplicationId = 10;

        $this->setUpSut(IrhpCandidatePermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.ecmtPermitApplication', 'epa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpApplication', 'ia')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf()
            ->shouldReceive('order')->once()->andReturnSelf();

        $query = GetList::create(
            [
                'ecmtPermitApplication' => $ecmtPermitApplicationId,
                'page' => 1,
                'limit' => 25,
                'order' => 'id',
                'sort' => 'ASC',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND epa.id = [['.$ecmtPermitApplicationId.']]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
