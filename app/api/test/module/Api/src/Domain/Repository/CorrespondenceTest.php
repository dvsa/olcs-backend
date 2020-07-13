<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\Correspondence
 */
class CorrespondenceTest extends RepositoryTestCase
{
    /** @var  Repository\Correspondence */
    protected $sut;

    public function setUp(): void
    {
        $this->setUpSut(Repository\Correspondence::class, true);
    }

    public function testApplyListMethods()
    {
        $orgId = 9999;

        $mockQb = $this->createMockQb('{{QUERY}}');

        $mockQry = m::mock(TransferQry\Correspondence\Correspondences::class)
            ->shouldReceive('getOrganisation')->once()->andReturn($orgId)
            ->getMock();

        $this->sut->applyListJoins($mockQb);
        $this->sut->applyListFilters($mockQb, $mockQry);

        static::assertEquals(
            '{{QUERY}} ' .
            'SELECT l, d ' .
            'INNER JOIN co.licence l ' .
            'INNER JOIN co.document d ' .
            'AND l.organisation = [[' . $orgId . ']]',
            $this->query
        );
    }
}
