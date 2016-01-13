<?php

/**
 * Grace Period Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Trailer;

use Dvsa\Olcs\Api\Domain\QueryHandler\Trailer\Trailer;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Trailer as TrailerRepo;
use Dvsa\Olcs\Transfer\Query\Trailer\Trailer as Qry;

/**
 * Grace Periods Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class TrailerTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Trailer();
        $this->mockRepo('Trailer', TrailerRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Trailer']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(
                [
                    [
                        'id' => 1
                    ],
                ]
            );

        $this->sut->handleQuery($query);
    }
}
