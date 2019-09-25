<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\AvailableLicences;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableLicences as AvailableLicencesQry;
use Dvsa\Olcs\Transfer\Query\Organisation\OrganisationAvailableLicences;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Identity\IdentityInterface;

class AvailableLicencesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableLicences();
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $permitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT;
        $stockId = 111;
        $licenceId = 666;
        $isNotYetSubmitted = true;
        $originalQuery = m::mock(AvailableLicencesQry::class);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('getAssociatedStock->getId')->once()->withNoArgs()->andReturn($stockId);
        $application->shouldReceive('getLicence->getId')->once()->withNoArgs()->andReturn($licenceId);
        $application->shouldReceive('isNotYetSubmitted')->once()->withNoArgs()->andReturn($isNotYetSubmitted);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->once()
            ->with($originalQuery)
            ->andReturn($application);

        $orgId = 1245;
        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getId')->andReturn($orgId);

        $identity = m::mock(IdentityInterface::class);
        $identity->shouldReceive('getUser->getOrganisationUsers->isEmpty')->once()->andReturn(false);
        $identity->shouldReceive('getUser->getRelatedOrganisation')->once()->andReturn($organisation);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($identity);

        $queryResult = ['eligibleLicences' => ['licences']];

        $this->queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(
                function ($organisationPermitsQry) use ($queryResult, $orgId, $permitTypeId, $stockId) {
                    /** @var OrganisationAvailableLicences $organisationPermitsQry */
                    $this->assertInstanceOf(OrganisationAvailableLicences::class, $organisationPermitsQry);
                    $data = $organisationPermitsQry->getArrayCopy();

                    $this->assertEquals($orgId, $data['id']);
                    $this->assertEquals($permitTypeId, $data['irhpPermitType']);
                    $this->assertEquals($stockId, $data['irhpPermitStock']);

                    return $queryResult;
                }
            );

        $additionalValues = [
            'selectedLicence' => $licenceId,
            'isNotYetSubmitted' => $isNotYetSubmitted,
        ];

        $expectedReturnValues = array_merge($queryResult, $additionalValues);

        $this->assertEquals($expectedReturnValues, $this->sut->handleQuery($originalQuery));
    }
}
