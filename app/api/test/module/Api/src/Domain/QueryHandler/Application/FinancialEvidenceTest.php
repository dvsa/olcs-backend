<?php

/**
 * Financial Evidence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\FinancialEvidence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\FinancialStandingRate as RateRepo;
use Dvsa\Olcs\Transfer\Query\Application\FinancialEvidence as Qry;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Financial Evidence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new FinancialEvidence();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockedSmServices['FinancialStandingHelperService'] = m::mock(FinancialStandingHelperService::class);

        return parent::setUp();
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

        $mockFinancialDocuments = m::mock()
            ->shouldReceive('toArray')
            ->andReturn(['DOCUMENTS'])
            ->once()
            ->getMock();

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

        $mockApplication = m::mock()
            ->shouldReceive('getApplicationDocuments')
            ->with('category', 'subCategory')
            ->andReturn($mockFinancialDocuments)
            ->once()
            ->shouldReceive('jsonSerialize')
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

        $expectedResult = [
            'id' => $applicationId,
            'documents' => ['DOCUMENTS'],
            'financialEvidence' => [
                'requiredFinance' => $totalRequired,
                'vehicles' => 9,
                'standardFirst' => 7000,
                'standardAdditional' => 3900,
                'restrictedFirst' => 3100,
                'restrictedAdditional' => 1700,
            ]
        ];

        $this->assertEquals($expectedResult, $this->sut->handleQuery($query));
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
            [111, 'lcat_gv', 'ltyp_sn', 3, Application::APPLICATION_STATUS_NOT_SUBMITTED], // shouldn't double-count
            [112, 'lcat_gv', 'ltyp_sn', 2, Application::APPLICATION_STATUS_UNDER_CONSIDERATION],
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

                return $mockApplication;
            },
            $values
        );
    }
}
