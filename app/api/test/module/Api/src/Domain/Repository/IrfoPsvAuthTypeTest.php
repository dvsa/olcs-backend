<?php

/**
 * IrfoPsvAuthType repo test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuthType as Repo;

/**
 * IrfoPsvAuthType repo test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IrfoPsvAuthTypeTest extends RepositoryTestCase
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

        $mockQb->shouldReceive('orderBy')->with('m.description', 'ASC')->once()->andReturnSelf();

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
