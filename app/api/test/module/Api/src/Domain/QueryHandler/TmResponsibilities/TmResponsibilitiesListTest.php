<?php

/**
 * TmResponsibilitiesList Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TmResonsibilities;

use Dvsa\Olcs\Api\Domain\QueryHandler\TmResponsibilities\TmResponsibilitiesList as QueryHandler;
use Dvsa\Olcs\Transfer\Query\TmResponsibilities\TmResponsibilitiesList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as TransportMangerApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TransportMangerLicenceRepo;

/**
 * TmResponsibilitiesList Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmResponsibilitiesListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TransportManagerApplication', TransportMangerApplicationRepo::class);
        $this->mockRepo('TransportManagerLicence', TransportMangerLicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(
            [
                'transportManager' => 1,
                'applicationStatuses' => 'a,b',
                'licenceStatuses' => 'c,d'
            ]
        );

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchForTransportManager')
            ->with(1, 'c,d')
            ->once()
            ->andReturn(['licences'])
            ->getMock();

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchForTransportManager')
            ->with(1, 'a,b', true)
            ->once()
            ->andReturn(['applications'])
            ->getMock();

        $this->assertEquals(
            [
                'result' => ['licences'],
                'count'  => 1,
                'tmApplications' => ['applications'],
                'tmApplicationsCount' => 1
            ],
            $this->sut->handleQuery($query)
        );
    }
}
