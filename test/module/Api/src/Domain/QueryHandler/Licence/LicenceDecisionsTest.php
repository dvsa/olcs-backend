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
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Mockery as m;

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

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['foo' => 'bar'], $result->serialize());
    }
}
