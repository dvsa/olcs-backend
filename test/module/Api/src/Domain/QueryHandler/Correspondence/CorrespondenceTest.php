<?php

/**
 * CorrespondenceTest.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\GracePeriod;

use Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence\Correspondence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Correspondence as CorrespondenceRepo;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondence as Qry;

/**
 * Correspondence Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class CorrespondenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Correspondence();
        $this->mockRepo('Correspondence', CorrespondenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Correspondence']
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
