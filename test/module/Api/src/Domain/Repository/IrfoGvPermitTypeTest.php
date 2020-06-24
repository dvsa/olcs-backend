<?php

/**
 * IrfoGvPermitType test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermitType as Repo;
use Mockery as m;

/**
 * IrfoGvPermitType test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IrfoGvPermitTypeTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(Repo::class);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $mockQ = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);
        $mockQb->shouldReceive('orderBy')->with('m.description', 'ASC')->once()->andReturnSelf();

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
