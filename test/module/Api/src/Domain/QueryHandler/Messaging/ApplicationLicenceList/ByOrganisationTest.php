<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList\ByOrganisation;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\ApplicationLicenceList\ByOrganisation as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;

class ByOrganisationTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new ByOrganisation();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('Licence', Repository\Licence::class);

        $this->mockedSmServices = ['SectionAccessService' => m::mock(), AuthorizationService::class => m::mock(AuthorizationService::class)->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true)->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)->getMock(),];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser->getId')->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([
            'organisation' => 1,
        ]);

        $licences = new ArrayIterator(
            [
                [ 'id' => 1, 'organisation_id' => 1, 'licNo' => 'A1' ],
                [ 'id' => 2, 'organisation_id' => 1, 'licNo' => 'B2' ],
                [ 'id' => 3, 'organisation_id' => 1, 'licNo' => 'C3' ],
            ]
        );

        $applications = new ArrayIterator(
            [
                [ 'id' => 1, 'licence_id' => 1 ],
                [ 'id' => 2, 'licence_id' => 2 ],
                [ 'id' => 3, 'licence_id' => 3 ],
            ]
        );

        $mockQb = m::mock(QueryBuilder::class);

        $this->repoMap['Licence']->shouldReceive('fetchByOrganisationIdAndStatuses')->andReturn($mockQb);
        $this->repoMap['Licence']->shouldReceive('fetchByOrganisationId')->andReturn($mockQb)->once()->andReturn($licences);
        $this->repoMap['Application']->shouldReceive('fetchByOrganisationIdAndStatuses')->andReturn($mockQb)->once()->andReturn($applications);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);
        $this->assertCount(2, $result['result']);

        $this->assertArrayHasKey('licences', $result['result']);
        $this->assertCount(3, $result['result']['licences']);

        $this->assertArrayHasKey('applications', $result['result']);
        $this->assertCount(3, $result['result']['applications']);
    }
}
