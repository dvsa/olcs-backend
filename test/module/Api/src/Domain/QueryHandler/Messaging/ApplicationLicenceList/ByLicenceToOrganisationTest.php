<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList\ByLicenceToOrganisation;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\ApplicationLicenceList\ByLicenceToOrganisation as Qry;
use Dvsa\Olcs\Transfer\Query\Messaging\ApplicationLicenceList\ByOrganisation;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;

class ByLicenceToOrganisationTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ByLicenceToOrganisation();
        $this->mockRepo('Licence', Repository\Licence::class);

        $this->mockedSmServices = ['SectionAccessService' => m::mock(), AuthorizationService::class => m::mock(AuthorizationService::class)->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true)->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)->getMock(),];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser->getId')->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([
            'licence' => 1,
        ]);

        $mockLicence = m::mock(LicenceEntity::class);

        $mockOrganisation = m::mock(OrganisationEntity::class);
        $mockOrganisation->shouldReceive('getId')->once()->andReturn(1);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(1)->andReturn($mockLicence);
        $mockLicence->shouldReceive('getOrganisation')->andReturn($mockOrganisation);

        $this->queryHandler->shouldReceive('handleQuery')->with(m::on(
            function ($argument) {
                $this->assertInstanceOf(ByOrganisation::class, $argument);
                assert($argument instanceof ByOrganisation);
                $this->assertEquals(1, $argument->getOrganisation());
                return true;
            }
        ))->once();

        $this->sut->handleQuery($query);
    }
}
