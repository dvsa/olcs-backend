<?php

/**
 * TxcInboxByBusRegTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr\TxcInboxByBusReg;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Licence as LicenceEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Transfer\Query\Bus\Ebsr\TxcInboxByBusReg as Qry;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * TxcInboxByBusRegTest
 */
class TxcInboxByBusRegTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TxcInboxByBusReg();
        $this->mockRepo('TxcInbox', TxcInboxRepo::class);

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
        $query = Qry::create(
            [
                'busReg' => 2
            ]
        );

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($this->getCurrentUser())
            ->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_EBSR_DOCUMENTS, null)
            ->once()->atLeast()
            ->andReturn(true);

        $mockLicence = m::mock();
        $mockLicence->shouldReceive('determineNpNumber')->andReturn('333');

        $mockResult = new TxcInboxEntity();
        $busReg = new BusRegEntity();
        $busReg = m::mock(BusRegEntity::class)->makePartial();
        $busReg->shouldReceive('isLatestVariation')->andReturn(false);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getLatestBusVariation');
        $licence->shouldReceive('determineNpNumber')
            ->once()
            ->andReturn('4321');

        $busReg->setLicence($licence);

        $mockResult->setBusReg($busReg);

/*        $mockResult->shouldReceive('fetchListForLocalAuthorityByBusReg')
            ->once()
            ->andReturn([0 => ['bar']]);
        $mockResult->shouldReceive('getLicence')->once()->andReturn($mockLicence);
*/
        $this->repoMap['TxcInbox']->shouldReceive('fetchListForLocalAuthorityByBusReg')
            ->with($query->getBusReg(), NULL)
            ->andReturn([0 => $mockResult]);

        $result = $this->sut->handleQuery($query);

    }
}
