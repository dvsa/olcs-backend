<?php

/**
 * BusRegWithDocumentsTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\BusRegWithDocuments;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Transfer\Query\Bus\BusRegWithDocuments as Qry;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * BusRegWithDocumentsTest
 */
class BusRegWithDocumentsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new BusRegWithDocuments();
        $this->mockRepo('Bus', BusRepo::class);

        $this->mockedSmServices = [
            'ZfcRbac\Service\AuthorizationService' => m::mock('ZfcRbac\Service\AuthorizationService')
        ];

        parent::setUp();
    }

    private function getCurrentUser()
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        $mockUser->shouldReceive('getLocalAuthority')
            ->andReturnNull();

        return $mockUser;
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($this->getCurrentUser())
            ->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_EBSR_DOCUMENTS, null)
            ->once()->atLeast()
            ->andReturn(true);

        $mockLicence = m::mock();
        $mockLicence->shouldReceive('determineNpNumber')->andReturn('333');

        $mockResult = m::mock(BusRegEntity::class)->makePartial();

        $mockResult->shouldReceive('fetchLatestUnreadBusRegDocumentsByLocalAuthority')
            ->once()
            ->andReturn(new \Doctrine\Common\Collections\ArrayCollection())
            ->shouldReceive('serialize')->andReturn(['bar']);
        $mockResult->shouldReceive('getLicence')->once()->andReturn($mockLicence);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockResult);

        $result = $this->sut->handleQuery($query);

    }
}
