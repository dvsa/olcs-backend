<?php

/**
 * TxcInboxByBusRegTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus\Ebsr;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr\TxcInboxByBusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as TxcInboxEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as TxcInboxRepo;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
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
        $this->mockRepo('Bus', BusRepo::class);

        $this->mockedSmServices = [
            'ZfcRbac\Service\AuthorizationService' => m::mock('ZfcRbac\Service\AuthorizationService')
        ];

        parent::setUp();
    }

    private function getCurrentUser($localAuthorityId = null, $organisationId = null)
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        if (!empty($localAuthorityId)) {
            $localAuthority = new \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority();
            $localAuthority->setId($localAuthorityId);
        } else {
            $localAuthority = null;
        }
        $mockUser->setLocalAuthority($localAuthority);

        $organisationUsers = new ArrayCollection();

        if (!empty($organisationId)) {
            $organisation = new Organisation();
            $organisation->setId($organisationId);

            $organisationUser = new OrganisationUser();

            $organisationUser->setOrganisation($organisation);
            $organisationUsers->add($organisationUser);
        }
        $mockUser->setOrganisationUsers($organisationUsers);

        return $mockUser;
    }

    public function testHandleQueryForOrganisation()
    {
        $busRegId = 2;
        $organisationId = 6;
        $query = Qry::create(
            [
                'busReg' => $busRegId
            ]
        );

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->andReturn($this->getCurrentUser(null, $organisationId));

        $mockLicence = m::mock();
        $mockLicence->shouldReceive('determineNpNumber')->andReturn('333');
        $mockLicence->shouldReceive('getLatestBusVariation')->andReturn(null);

        $mockResult = new TxcInboxEntity();
        $busReg = new BusRegEntity();
        $busReg->setLicence($mockLicence);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getLatestBusVariation');
        $licence->shouldReceive('determineNpNumber')
            ->once()
            ->andReturn('4321');

        $busReg->setLicence($licence);

        $mockResult->setBusReg($busReg);

        $this->repoMap['TxcInbox']->shouldReceive('fetchListForOrganisationByBusReg')
            ->with($query->getBusReg(), $organisationId)
            ->andReturn([0 => $mockResult]);

        $this->repoMap['Bus']->shouldReceive('fetchById')
            ->with($query->getBusReg())
            ->andReturn($busReg);

        $this->sut->handleQuery($query);
    }

    public function testHandleQueryForLocalAuthority()
    {
        $busRegId = 2;
        $localAuthorityId = 4;
        $query = Qry::create(
            [
                'busReg' => $busRegId
            ]
        );

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->andReturn($this->getCurrentUser($localAuthorityId));

        $mockLicence = m::mock();
        $mockLicence->shouldReceive('determineNpNumber')->andReturn('333');
        $mockLicence->shouldReceive('getLatestBusVariation')->andReturn(null);

        $mockResult = new TxcInboxEntity();

        $busReg = new BusRegEntity();
        $busRegShortNotice = new BusNoticePeriod();
        $busRegShortNotice->setId(BusRegEntity::NOTICE_PERIOD_OTHER);
        $busReg->setLicence($mockLicence);
        $busReg->setShortNotice($busRegShortNotice);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getLatestBusVariation');
        $licence->shouldReceive('determineNpNumber')
            ->once()
            ->andReturn('4321');

        $busReg->setLicence($licence);

        $mockResult->setBusReg($busReg);

        $this->repoMap['TxcInbox']->shouldReceive('fetchListForLocalAuthorityByBusReg')
            ->with($query->getBusReg(), $localAuthorityId)
            ->andReturn([0 => $mockResult]);

        $this->repoMap['Bus']->shouldReceive('fetchById')
            ->with($query->getBusReg())
            ->andReturn($busReg);

        $this->sut->handleQuery($query);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function testHandleQueryBusRegNotFoundException()
    {
        $busRegId = 2;
        $query = Qry::create(
            [
                'busReg' => $busRegId
            ]
        );

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity')
            ->andReturn($this->getCurrentUser(4));

        $this->repoMap['TxcInbox']->shouldReceive('fetchListForLocalAuthorityByBusReg')
            ->with($query->getBusReg(), 4)
            ->andReturnNull();

        $this->repoMap['Bus']->shouldReceive('fetchById')
            ->with($query->getBusReg())
            ->andReturnNull();

        $this->sut->handleQuery($query);
    }
}
