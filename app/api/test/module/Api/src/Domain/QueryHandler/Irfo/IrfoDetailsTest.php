<?php

/**
 * IrfoDetails Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoDetails;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoDetails as Qry;

/**
 * IrfoDetails Test
 */
class IrfoDetailsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoDetails();
        $this->mockRepo('Organisation', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Organisation']->shouldReceive('fetchIrfoDetailsUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
