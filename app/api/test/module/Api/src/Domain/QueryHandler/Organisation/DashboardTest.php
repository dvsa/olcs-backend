<?php

/**
 * Dashboard Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\Dashboard;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Correspondence as CorrespondenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
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
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Correspondence', CorrespondenceRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

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
        $mockOrganisation
            ->shouldReceive('serialize')->once()->andReturn(['id' => $organisationId])
            ->getMock();

        $mockLicence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->setId($licenceId);
        $mockLicence
            ->shouldReceive('serialize')->once()->andReturn(['id' => $licenceId]);

        $mockApplication = m::mock(ApplicationEntity::class)
            ->makePartial()
            ->setId($applicationId);
        $mockApplication
            ->shouldReceive('serialize')->once()->andReturn(['id' => $applicationId]);

        $mockVariation = m::mock(ApplicationEntity::class)
            ->makePartial()
            ->setId($variationId)
            ->setIsVariation(true);
        $mockVariation
            ->shouldReceive('getStatus->getId')
            ->andReturn(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED);
        $mockVariation
            ->shouldReceive('serialize')->andReturn(['id' => $variationId]);

        $licences = new ArrayCollection();
        $licences->add($mockLicence);

        $mockOrganisation
            ->shouldReceive('getActiveLicences')
            ->once()
            ->andReturn($licences);

        $this->repoMap['Organisation']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockOrganisation);

        $this->repoMap['Application']
            ->shouldReceive('fetchByOrganisationIdAndStatuses')
            ->with(
                $organisationId,
                [
                    ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                    ApplicationEntity::APPLICATION_STATUS_GRANTED,
                    ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED
                ]
            )
            ->once()
            ->andReturn([$mockApplication, $mockVariation]);

        $this->repoMap['Correspondence']
            ->shouldReceive('getUnreadCountForOrganisation')
            ->with($organisationId)
            ->andReturn(99);

        $this->repoMap['Fee']
            ->shouldReceive('getOutstandingFeeCountByOrganisationId')
            ->with($organisationId, true)
            ->andReturn(123);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $expected = [
            'id' => 69,
            'dashboard' => [
                'licences' => [
                    ['id' => 7],
                ],
                'applications' => [
                    ['id' => 1],
                ],
                'variations' =>[
                    [ 'id' => 2],
                ],
                'correspondenceCount' => 99,
                'feeCount' => 123,
            ],
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
