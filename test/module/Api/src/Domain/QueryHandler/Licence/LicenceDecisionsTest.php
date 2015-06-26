<?php

/**
 * LicenceDecisionsTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\LicenceDecisions;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\LicenceDecisions as Qry;

/**
 * Licence Decisions Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceDecisionsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new LicenceDecisions();
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['licence' => 1]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
