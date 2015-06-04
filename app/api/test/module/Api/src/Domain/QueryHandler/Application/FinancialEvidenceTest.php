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
use Dvsa\Olcs\Transfer\Query\Application\Application as Qry;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
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
        $licenceType = Licence::LICENCE_TYPE_STANDARD_NATIONAL;
        $goodsOrPsv = Licence::LICENCE_CATEGORY_GOODS_VEHICLE;
        $totAuthVehicles = 10;
        $organisationLicences = [];
        $organisationApplications = [];
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

        $result = [
            'id' => $applicationId,
            'documents' => ['DOCUMENTS'],
            'financialEvidence' => [
                'requiredFinance' => 32100,
                'vehicles' => 10,
                'standardFirst' => 6000,
                'standardAdditional' => 2900,
                'restrictedFirst' => 2100,
                'restrictedAdditional' => 700,
            ]
        ];
        $this->assertEquals($result, $this->sut->handleQuery($query));
    }

    protected function getStubRates()
    {
        return [
            $this->getStubRate(6000, 2900, 'lcat_gv', 'ltyp_sn'),
            $this->getStubRate(6000, 2900, 'lcat_gv', 'ltyp_si'),
            $this->getStubRate(2100, 700, 'lcat_gv', 'ltyp_r'),
            $this->getStubRate(7000, 3900, 'lcat_psv', 'ltyp_sn'),
            $this->getStubRate(7000, 3900, 'lcat_psv', 'ltyp_si'),
            $this->getStubRate(3100, 1700, 'lcat_psv', 'ltyp_r'),
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
