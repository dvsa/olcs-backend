<?php

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CompanySubsidiary;

use Dvsa\Olcs\Api\Domain\QueryHandler\CompanySubsidiary\CompanySubsidiary;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\CompanySubsidiary as CompanySubsidiaryRepo;
use Dvsa\Olcs\Transfer\Query\CompanySubsidiary\CompanySubsidiary as Qry;

/**
 * Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiaryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CompanySubsidiary();
        $this->mockRepo('CompanySubsidiary', CompanySubsidiaryRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['CompanySubsidiary']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
