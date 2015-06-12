<?php

/**
 * CorrespondencesTest.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\GracePeriod;

use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence\Correspondences;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Correspondence as CorrespondenceRepo;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences as Qry;

/**
 * Correspondences Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class CorrespondencesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Correspondences();
        $this->mockRepo('Correspondence', CorrespondenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['licence' => 1]);

        $this->repoMap['Correspondence']
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
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
            ->shouldReceive('fetchCount');

        $this->sut->handleQuery($query);
    }
}
