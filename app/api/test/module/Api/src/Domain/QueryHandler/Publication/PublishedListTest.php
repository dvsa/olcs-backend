<?php

/**
 * PendingList Test
 *
 * @author Richard Ward <richard.ward@bjss.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Publication;

use Dvsa\Olcs\Api\Domain\QueryHandler\Publication\PublishedList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Transfer\Query\Publication\PendingList as Qry;
use Mockery as m;

/**
 * PendingList Test
 *
 * @author Richard Ward <richard.ward@bjss.com>
 */
class PublishedListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PublishedList();
        $this->mockRepo('Publication', PublicationRepo::class);

        parent::setUp();
    }

    /**
     * tests retrieving a list of published publications (status published)
     */
    public function testHandleQuery()
    {
        $count = 25;
        $query = Qry::create([]);
        $serializedResult = 'foo';

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn($serializedResult);

        $queryResult = [
            'results' => [0 =>$mockResult],
            'count' => $count
        ];

        $this->repoMap['Publication']->shouldReceive('fetchPublishedList')
            ->andReturn($queryResult);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], $count);
        $this->assertEquals($result['result'], [$serializedResult]);
    }
}
