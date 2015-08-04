<?php

/**
 * GetForResponsibilities Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication\GetForResponsibilities as QueryHandler;
use Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetForResponsibilities as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as TransportMangerApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\OtherLicence as OtherLicenceRepo;

/**
 * GetForResponsibilities Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetForResponsibilitiesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TransportManagerApplication', TransportMangerApplicationRepo::class);
        $this->mockRepo('OtherLicence', OtherLicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1]);

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchForResponsibilities')
            ->with(1)
            ->once()
            ->andReturn(['tma'])
            ->getMock();

        $this->repoMap['OtherLicence']
            ->shouldReceive('fetchForTransportManagerApplication')
            ->with(1)
            ->once()
            ->andReturn(['otherLicences'])
            ->getMock();

        $this->assertEquals(
            [
                'result' => ['tma'],
                'count'  => 1,
                'otherLicences' => ['otherLicences']
            ],
            $this->sut->handleQuery($query)
        );
    }
}
