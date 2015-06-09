<?php

/**
 * GetDetailsTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication\GetDetails as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as Repo;
use Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetDetails as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

/**
 * GetDetailsTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetDetailsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('ApplicationOperatingCentre', Repo::class);
        $this->mockRepo('LicenceOperatingCentre', Repo::class);
        $this->mockRepo('PreviousConviction', Repo::class);
        $this->mockRepo('OtherLicence', Repo::class);
        $this->mockRepo('TmEmployment', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 32]);

        //$this->repoMap['TransportManagerApplication']->shouldReceive('fetchDetails')->with(32)->andReturn('ENTITY');

        //$result = $this->sut->handleQuery($query);

        //$this->assertSame('ENTITY', $result);
    }
}
