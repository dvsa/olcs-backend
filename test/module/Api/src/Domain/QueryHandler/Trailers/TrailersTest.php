<?php

/**
 * Trailer Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Trailers;

use Dvsa\Olcs\Api\Domain\QueryHandler\Trailer\Trailers;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Trailer as TrailerRepo;
use Dvsa\Olcs\Transfer\Query\Trailer\Trailers as Qry;

/**
 * Trailer Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class TrailersTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Trailers();
        $this->mockRepo('Trailer', TrailerRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['licence' => 1]);

        $this->repoMap['Trailer']->shouldReceive('fetchByLicenceId')
            ->with($query)
            ->andReturn(
                [
                    [
                        'id' => 1
                    ],
                    [
                        'id' => 2
                    ]
                ]
            )
            ->shouldReceive('fetchCount')
            ->andReturn(2);

        $this->assertEquals(
            [
                'result' => [
                    [
                        'id' => 1
                    ],[
                        'id' => 2
                    ]
                ],
                'count' => 2,
            ],
            $this->sut->handleQuery($query)
        );
    }
}
