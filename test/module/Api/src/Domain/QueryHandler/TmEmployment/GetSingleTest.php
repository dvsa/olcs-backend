<?php

/**
 * GetSingleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\TmEmployment\GetSingle as QueryHandler;
use Dvsa\Olcs\Transfer\Query\TmEmployment\GetSingle as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;

/**
 * GetSingleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetSingleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TmEmployment', \Dvsa\Olcs\Api\Domain\Repository\TmEmployment::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1066]);

        $this->repoMap['TmEmployment']->shouldReceive('fetchUsingId')->with($query)->once()->andReturn('RESULT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame('RESULT', $result);
    }
}
