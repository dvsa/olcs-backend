<?php

/**
 * PiVenueTest
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\PiVenue as Repo;
use Mockery as m;

/**
 * PiVenue Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PiVenueTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $mockQ = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);
        $mockQ->shouldReceive('getTrafficArea')->once()->andReturn('B');

        $mockQb->shouldReceive('expr->eq')->with('m.trafficArea', ':trafficArea')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('trafficArea', 'B')->once();

        $mockQb->shouldReceive('orderBy')->with('m.name', 'ASC')->once()->andReturnSelf();

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
