<?php

/**
 * Impounding Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Impounding;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Impounding as ImpoundingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Impounding as Qry;

/**
 * Impounding Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ImpoundingTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Impounding();
        $this->mockRepo('Impounding', ImpoundingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Impounding']->shouldReceive('fetchCaseImpoundingUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result, ['foo']);
    }
}
