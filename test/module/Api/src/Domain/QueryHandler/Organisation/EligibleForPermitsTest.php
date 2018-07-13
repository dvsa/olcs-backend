<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\EligibleForPermits as EligibleForPermitsHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\EligibleForPermits as EligibleForPermitsQry;
use Mockery as m;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * EligibleForPermits Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EligibleForPermitsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new EligibleForPermitsHandler();
        $this->mockRepo('Organisation', OrganisationRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
        ];

        parent::setUp();
    }

    public function testHandleQueryUsingProvidedId()
    {
        $isEligible = false;
        $orgId = 111;
        $query = EligibleForPermitsQry::create(['id' => $orgId]);

        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('isEligibleForPermits')->once()->withNoArgs()->andReturn($isEligible);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->once()->with($query)->andReturn($organisation);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($isEligible, $result['eligibleForPermits']);
    }

    public function testHandleQueryUsingOrgFromUser()
    {
        $isEligible = true;
        $query = EligibleForPermitsQry::create([]);

        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('isEligibleForPermits')->once()->withNoArgs()->andReturn($isEligible);

        $identityInterface = m::mock(IdentityInterface::class);
        $identityInterface->shouldReceive('getUser->getOrganisationUsers->isEmpty')
            ->once()
            ->withNoArgs()
            ->andReturn(false);
        $identityInterface->shouldReceive('getUser->getRelatedOrganisation')
            ->once()
            ->andReturn($organisation);

        //this user will produce an organisation
        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->withNoArgs()
            ->andReturn($identityInterface);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($isEligible, $result['eligibleForPermits']);
    }

    public function testHandleQueryWhenUserHasNoOrg()
    {
        $query = EligibleForPermitsQry::create([]);

        //we won't be able to find an organisation for this user
        $identityInterface = m::mock(IdentityInterface::class);
        $identityInterface->shouldReceive('getUser->getOrganisationUsers->isEmpty')
            ->once()
            ->andReturn(true);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($identityInterface);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(false, $result['eligibleForPermits']);
    }
}
