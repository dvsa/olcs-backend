<?php

/**
 * FStandingCapitalReserves Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bookmark;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark\FStandingCapitalReserves;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\FStandingCapitalReserves as Qry;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;

/**
 * FStandingCapitalReserves Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FStandingCapitalReservesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new FStandingCapitalReserves();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Organisation', OrganisationRepo::class);

        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock(FinancialStandingHelperService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $organisationId = 69;
        $query = Qry::create(
            [
                'organisation' => $organisationId,
            ]
        );

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('serialize')
            ->with([])
            ->andReturn(['id' => 111]);

        $application1 = m::mock(ApplicationEntity::class)->makePartial()->setId(1);
        $application1->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_gv');
        $application1->shouldReceive('getLicenceType->getId')->andReturn('ltyp_sn');
        $application1->shouldReceive('getTotAuthVehicles')->andReturn(4);

        $application2 = m::mock(ApplicationEntity::class)->makePartial()->setId(2);
        $application2->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_gv');
        $application2->shouldReceive('getLicenceType->getId')->andReturn('ltyp_si');
        $application2->shouldReceive('getTotAuthVehicles')->andReturn(5);

        $licence1 = m::mock(LicenceEntity::class)->makePartial()->setId(1);
        $licence1->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_psv');
        $licence1->shouldReceive('getLicenceType->getId')->andReturn('ltyp_sn');
        $licence1->shouldReceive('getTotAuthVehicles')->andReturn(6);

        $licence2 = m::mock(LicenceEntity::class)->makePartial()->setId(2);
        $licence2->shouldReceive('getGoodsOrPsv->getId')->andReturn('lcat_psv');
        $licence2->shouldReceive('getLicenceType->getId')->andReturn('ltyp_r');
        $licence2->shouldReceive('getTotAuthVehicles')->andReturn(7);

        $this->repoMap['Application']
            ->shouldReceive('fetchActiveForOrganisation')
            ->with(69)
            ->andReturn([$application1, $application2]);

        $organisation = m::mock(OrganisationEntity::class)->makePartial()->setId($organisationId);
        $this->repoMap['Organisation']
            ->shouldReceive('fetchById')
            ->with($organisationId)
            ->andReturn($organisation);

        $organisation
            ->shouldReceive('getActiveLicences')
            ->andReturn([$licence1, $licence2]);

        $expectedAuths = [
            [
                'type' => 'ltyp_sn',
                'count' => 4,
                'category' => 'lcat_gv',
            ],
            [
                'type' => 'ltyp_si',
                'count' => 5,
                'category' => 'lcat_gv',
            ],
            [
                'type' => 'ltyp_sn',
                'count' => 6,
                'category' => 'lcat_psv',
            ],
            [
                'type' => 'ltyp_r',
                'count' => 7,
                'category' => 'lcat_psv',
            ],
        ];

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculation')
            ->once()
            ->with($expectedAuths)
            ->andReturn(1234);

        $this->assertEquals(1234, $this->sut->handleQuery($query));
    }
}
