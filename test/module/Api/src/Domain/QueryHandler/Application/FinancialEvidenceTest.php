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
        $this->mockRepo('FinancialStandingRate', RateRepo::class);

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
        $rates = $this->getStubRates();

        $query = Qry::create(['id' => $applicationId]);

        $mockFinancialDocuments = m::mock()
            ->shouldReceive('toArray')
            ->andReturn(['DOCUMENTS'])
            ->once()
            ->getMock();

        $mockLicenceType = m::mock()
            ->shouldReceive('getId')
            // ->once()
            ->andReturn($licenceType)
            ->getMock();

        $mockGoodsOrPsv = m::mock()
            ->shouldReceive('getId')
            ->andReturn($goodsOrPsv)
            ->getMock();

        $mockOrganisation = m::mock()
            ->shouldReceive('getLicences')
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
            ->shouldReceive('fetchForOrganisation')
            ->with($organisationId)
            ->once()
            ->andReturn($organisationApplications);

        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('getRatesInEffect')
            ->once()
            ->andReturn($rates);

        // For an operator:
        //  * with a goods standard international application with 3 vehicles,
        //    the finance is £7000 + (2 x £3900) = £14,800
        //  * plus a goods restricted licence with 3 vehicles, the finance is (3 x £1700) = £5,100
        //  * plus a psv restricted licence with 1 vehicle, the finance is £2,700
        //  * plus another goods app with 2 vehicles (2 x 3900) = £7,800
        //  * The total required finance is £14,800 + £5,100 + £2,700 + £7,800 = £30,400
        $expected = 30400;
        $result = [
            'id' => $applicationId,
            'documents' => ['DOCUMENTS'],
            'financialEvidence' => [
                'requiredFinance' => $expected,
                'vehicles' => 9,
                'standardFirst' => 7000,
                'standardAdditional' => 3900,
                'restrictedFirst' => 3100,
                'restrictedAdditional' => 1700,
            ]
        ];
        $this->assertEquals($result, $this->sut->handleQuery($query));
    }

    protected function getMockOrganisationLicences()
    {
        $values = [
            // id, category, type, vehicle auth, status
            [7, 'lcat_gv', 'ltyp_sn', 3, Licence::LICENCE_STATUS_VALID], // current app licence, should be ignored
            [8, 'lcat_gv', 'ltyp_r', 3, Licence::LICENCE_STATUS_VALID],
            [9, 'lcat_psv', 'ltyp_r', 1, Licence::LICENCE_STATUS_VALID],
            [10, 'lcat_gv', 'ltyp_sn', 5, Licence::LICENCE_STATUS_NOT_SUBMITTED], // invalid status, should be ignored
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
            [113, 'lcat_gv', 'ltyp_sn', 5, Licence::LICENCE_STATUS_NOT_SUBMITTED], // invalid status, should be ignored
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

    protected function getStubRates()
    {
        return [
            $this->getStubRate(7000, 3900, 'lcat_gv', 'ltyp_sn'),
            $this->getStubRate(7000, 3900, 'lcat_gv', 'ltyp_si'),
            $this->getStubRate(3100, 1700, 'lcat_gv', 'ltyp_r'),
            $this->getStubRate(8000, 4900, 'lcat_psv', 'ltyp_sn'),
            $this->getStubRate(8000, 4900, 'lcat_psv', 'ltyp_si'),
            $this->getStubRate(4100, 2700, 'lcat_psv', 'ltyp_r'),
        ];
    }

    protected function getStubRate($firstVehicleRate, $additionalVehicleRate, $goodsOrPsv, $licenceType)
    {
        $rate = new FinancialStandingRate();
        $goodsOrPsvChild = new RefData();
        $goodsOrPsvChild->setId($goodsOrPsv);
        $licenceTypeChild = new RefData();
        $licenceTypeChild->setId($licenceType);

        $rate
            ->setFirstVehicleRate($firstVehicleRate)
            ->setAdditionalVehicleRate($additionalVehicleRate)
            ->setGoodsOrPsv($goodsOrPsvChild)
            ->setLicenceType($licenceTypeChild);

        return $rate;
    }
}
