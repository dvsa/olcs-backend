<?php

/**
 * Financial Evidence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\FinancialEvidence;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Query\Application\FinancialEvidence as Qry;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Financial Evidence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidenceTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new FinancialEvidence();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock(FinancialStandingHelperService::class);
        $mockedAuth = m::mock(AuthorizationService::class)->makePartial();
        $this->mockedSmServices[AuthorizationService::class] = $mockedAuth;

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $applicationId = 111;
        $organisationId = 69;
        $applicationLicenceId = 7;
        $licenceType = Licence::LICENCE_TYPE_STANDARD_NATIONAL;
        $goodsOrPsv = Licence::LICENCE_CATEGORY_GOODS_VEHICLE;
        $totAuthVehicles = 3;
        $organisationLicences = $this->getMockOrganisationLicences();
        $organisationApplications = $this->getMockOrganisationApplications();
        $totalRequired = 30400;

        $query = Qry::create(['id' => $applicationId]);

        $mockLicenceType = m::mock()
            ->shouldReceive('getId')
            ->andReturn($licenceType)
            ->getMock();

        $mockGoodsOrPsv = m::mock()
            ->shouldReceive('getId')
            ->andReturn($goodsOrPsv)
            ->getMock();

        $mockOrganisation = m::mock()
            ->shouldReceive('getActiveLicences')
            ->andReturn($organisationLicences)
            ->shouldReceive('getId')
            ->andReturn($organisationId)
            ->getMock();

        $mockLicence = m::mock()
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrganisation)
            ->shouldReceive('getId')
            ->andReturn($applicationLicenceId)
            ->getMock();

        $mockDocument = m::mock()
            ->shouldReceive('serialize')
            ->with([])
            ->once()
            ->andReturn(['doc' => 'bar'])
            ->getMock();

        $mockApplication = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('getApplicationDocuments')
            ->with('category', 'subCategory')
            ->andReturn([$mockDocument])
            ->once()
            ->shouldReceive('serialize')
            ->andReturn(['id' => $applicationId])
            ->once()
            ->shouldReceive('getLicenceType')
            ->andReturn($mockLicenceType)
            ->shouldReceive('getTotAuthVehicles')
            ->andReturn($totAuthVehicles)
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn($mockGoodsOrPsv)
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->shouldReceive('getId')
            ->andReturn($applicationId)
            ->shouldReceive('getOtherActiveLicencesForOrganisation')
            ->andReturn(
                array_filter(
                    $organisationLicences,
                    function ($licence) use ($applicationLicenceId) {
                        return $licence->getId() !== $applicationLicenceId;
                    }
                )
            )
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($mockApplication)
            ->once()
            ->shouldReceive('getCategoryReference')
            ->with(Category::CATEGORY_APPLICATION)
            ->andReturn('category')
            ->once()
            ->shouldReceive('getSubCategoryReference')
            ->with(SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL)
            ->andReturn('subCategory')
            ->once()
            ->shouldReceive('fetchActiveForOrganisation')
            ->with($organisationId)
            ->atLeast(1)
            ->andReturn($organisationApplications);

        $this->mockedSmServices['FinancialStandingHelperService']
            ->shouldReceive('getFinanceCalculation')
            ->andReturn($totalRequired)
            ->shouldReceive('getRatesForView')
            ->with($goodsOrPsv)
            ->andReturn(
                [
                    'standardFirst' => 7000,
                    'standardAdditional' => 3900,
                    'restrictedFirst' => 3100,
                    'restrictedAdditional' => 1700,
                ]
            );
        $mockedId = m::mock(IdentityInterface::class)->shouldReceive('getUser')->andReturn(
            m::mock(User::class)->shouldReceive('getRoles')->andReturn(new ArrayCollection([]))->getMock()
        )->getMock();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity')->andReturn($mockedId);


        $expectedResult = [
            'id' => $applicationId,
            'documents' => [['doc' => 'bar']],
            'financialEvidence' => [
                'requiredFinance' => $totalRequired,
                'standardFirst' => 7000,
                'standardAdditional' => 3900,
                'restrictedFirst' => 3100,
                'restrictedAdditional' => 1700,
                'applicationVehicles' => 3,
                'otherLicenceVehicles' => 4,
                'otherApplicationVehicles' => 2,
            ]
        ];

        $this->assertEquals($expectedResult, $this->sut->handleQuery($query)->serialize());
    }

    protected function getMockOrganisationLicences()
    {
        $values = [
            // id, category, type, vehicle auth, status
            [7, 'lcat_gv', 'ltyp_sn', 3, Licence::LICENCE_STATUS_VALID], // current app licence, should be ignored
            [8, 'lcat_gv', 'ltyp_r', 3, Licence::LICENCE_STATUS_VALID],
            [9, 'lcat_psv', 'ltyp_r', 1, Licence::LICENCE_STATUS_VALID],
        ];

        return array_map(
            function ($value) {
                $mockLicence = m::mock();
                $mockLicence
                    ->shouldReceive('getId')
                    ->andReturn($value[0])
                    ->shouldReceive('getTotAuthVehicles')
                    ->andReturn($value[3]);

                // can't chain demeter expectations :-/
                $mockLicence
                    ->shouldReceive('getGoodsOrPsv->getId')
                    ->andReturn($value[1]);
                $mockLicence
                    ->shouldReceive('getLicenceType->getId')
                    ->andReturn($value[2]);
                $mockLicence
                    ->shouldReceive('getStatus->getId')
                    ->andReturn($value[4]);

                return $mockLicence;
            },
            $values
        );
    }

    protected function getMockOrganisationApplications()
    {
        $values = [
            // id, category, type, vehicle auth, status
            [111, 'lcat_gv', 'ltyp_sn', 3, Application::APPLICATION_STATUS_NOT_SUBMITTED, 0], // shouldn't double-count
            [112, 'lcat_gv', 'ltyp_sn', 2, Application::APPLICATION_STATUS_UNDER_CONSIDERATION, 0],
            [113, 'lcat_gv', 'ltyp_sn', 9, Application::APPLICATION_STATUS_UNDER_CONSIDERATION, 1], // variation
        ];

        return array_map(
            function ($value) {
                $mockApplication = m::mock();
                $mockApplication
                    ->shouldReceive('getId')
                    ->andReturn($value[0])
                    ->shouldReceive('getTotAuthVehicles')
                    ->andReturn($value[3]);

                $mockApplication
                    ->shouldReceive('getGoodsOrPsv->getId')
                    ->andReturn($value[1]);
                $mockApplication
                    ->shouldReceive('getLicenceType->getId')
                    ->andReturn($value[2]);
                $mockApplication
                    ->shouldReceive('getStatus->getId')
                    ->andReturn($value[4]);

                $mockApplication
                    ->shouldReceive('isVariation')
                    ->andReturn($value[5]);

                return $mockApplication;
            },
            $values
        );
    }
}
