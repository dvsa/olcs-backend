<?php

/**
 * Dashboard Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\Dashboard;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\Dashboard as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Dashboard Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DashboardTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Dashboard();
        $this->mockRepo('Organisation', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $organisationId = 69;
        $licenceId = 7;
        $applicationId = 1;
        $variationId = 2;

        $query = Qry::create(['id' => $organisationId]);

        $mockOrganisation = m::mock(OrganisationEntity::class)
            ->makePartial()
            ->setId($organisationId);

        $mockLicence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->setId($licenceId);
        $mockLicence
            ->shouldReceive('getStatus->getId')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID);

        $mockApplication = m::mock(ApplicationEntity::class)
            ->makePartial()
            ->setId($applicationId);
        $mockApplication
            ->shouldReceive('getStatus->getId')
            ->andReturn(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION);

        $mockVariation = m::mock(ApplicationEntity::class)
            ->makePartial()
            ->setId($variationId)
            ->setIsVariation(true);
        $mockVariation
            ->shouldReceive('getStatus->getId')
            ->andReturn(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED);

        $applications = new ArrayCollection();
        $applications->add($mockApplication);
        $applications->add($mockVariation);
        $mockLicence->setApplications($applications);

        $licences = new ArrayCollection();
        $licences->add($mockLicence);

        $mockOrganisation->setLicences($licences);

        $this->repoMap['Organisation']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockOrganisation);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        // @TODO serialize and assert content
    }
}
