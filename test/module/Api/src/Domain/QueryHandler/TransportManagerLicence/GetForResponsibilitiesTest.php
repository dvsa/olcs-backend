<?php

/**
 * GetForResponsibilities Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TransportManagerLicence;

use Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerLicence\GetForResponsibilities as QueryHandler;
use Dvsa\Olcs\Transfer\Query\TransportManagerLicence\GetForResponsibilities as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TransportMangerLicenceRepo;
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
        $this->mockRepo('TransportManagerLicence', TransportMangerLicenceRepo::class);
        $this->mockRepo('OtherLicence', OtherLicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1]);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchForResponsibilities')
            ->with(1)
            ->once()
            ->andReturn(['tml'])
            ->getMock();

        $this->repoMap['OtherLicence']
            ->shouldReceive('fetchForTransportManagerLicence')
            ->with(1)
            ->once()
            ->andReturn(['otherLicences'])
            ->getMock();

        $this->assertEquals(
            [
                'result' => ['tml'],
                'count'  => 1,
                'otherLicences' => ['otherLicences']
            ],
            $this->sut->handleQuery($query)
        );
    }
}
