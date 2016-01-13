<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TransportManagerLicence;

use Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerLicence\GetListByVariation as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as Repo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Query\TransportManagerLicence\GetListByVariation as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * GetListByVariation test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetListByVariationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TransportManagerLicence', Repo::class);
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['variation' => 1]);

        $mockTml = m::mock();
        $mockApplication = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(2)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchById')
            ->with(1)
            ->once()
            ->andReturn($mockApplication);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchByLicence')
            ->with(2)
            ->once()
            ->andReturn([$mockTml]);

        $mockTml->shouldReceive('serialize')->with(
            [
                'licence' => [
                    'status',
                    'licenceType',
                ],
                'transportManager' => [
                    'homeCd' => [
                        'person',
                    ],
                    'tmType',
                ]
            ]
        )->once()->andReturn('RESULT');

        $this->assertEquals(['result' => ['RESULT'], 'count' => 1], $this->sut->handleQuery($query));
    }
}
