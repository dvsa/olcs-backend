<?php

/**
 * Recipient Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Publication;

use Dvsa\Olcs\Api\Domain\QueryHandler\Publication\Recipient;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Recipient as RecipientRepo;
use Dvsa\Olcs\Transfer\Query\Publication\Recipient as Qry;

/**
 * Recipient Test
 */
class RecipientTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Recipient();
        $this->mockRepo('Recipient', RecipientRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Recipient']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
