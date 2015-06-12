<?php

/**
 * RecipientList Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Publication;

use Dvsa\Olcs\Api\Domain\QueryHandler\Publication\RecipientList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Recipient as RecipientRepo;
use Dvsa\Olcs\Transfer\Query\Publication\RecipientList as Qry;

/**
 * RecipientList Test
 */
class RecipientListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RecipientList();
        $this->mockRepo('Recipient', RecipientRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['Recipient']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['Recipient']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
