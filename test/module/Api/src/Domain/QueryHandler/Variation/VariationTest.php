<?php

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Variation\Variation;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Query\Variation\Variation as Qry;

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Variation();
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
