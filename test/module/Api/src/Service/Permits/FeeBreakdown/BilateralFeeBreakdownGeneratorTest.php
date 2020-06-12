<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\FeeBreakdown;

use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\FeeBreakdown\BilateralFeeBreakdownGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * BilateralFeeBreakdownGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class BilateralFeeBreakdownGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $feeTypeRepo = m::mock(FeeTypeRepository::class);

        $bilateralPermitUsageSelection1 = RefData::JOURNEY_SINGLE;
        $countryDesc1 = 'Spain';
        $filteredBilateralRequired1 = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 5,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 8,
        ];

        $productReference1StandardApplication = 'PRODUCT_REFERENCE_1_STANDARD_APPLICATION';
        $productReference1StandardIssue = 'PRODUCT_REFERENCE_1_STANDARD_ISSUE';
        $productReference1CabotageApplication = 'PRODUCT_REFERENCE_1_CABOTAGE_APPLICATION';
        $productReference1CabotageIssue = 'PRODUCT_REFERENCE_1_CABOTAGE_ISSUE';

        $applicationFeeType1StandardApplication = m::mock(FeeType::class);
        $applicationFeeType1StandardIssue = m::mock(FeeType::class);
        $feePerPermit1Standard = 45;

        $applicationFeeType1CabotageApplication = m::mock(FeeType::class);
        $applicationFeeType1CabotageIssue = m::mock(FeeType::class);
        $feePerPermit1Cabotage = 60;

        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with($productReference1StandardApplication)
            ->andReturn($applicationFeeType1StandardApplication);
        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with($productReference1StandardIssue)
            ->andReturn($applicationFeeType1StandardIssue);
        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with($productReference1CabotageApplication)
            ->andReturn($applicationFeeType1CabotageApplication);
        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with($productReference1CabotageIssue)
            ->andReturn($applicationFeeType1CabotageIssue);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($bilateralPermitUsageSelection1);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getCountry->getCountryDesc')
            ->withNoArgs()
            ->andReturn($countryDesc1);
        $irhpPermitApplication1->shouldReceive('getFilteredBilateralRequired')
            ->withNoArgs()
            ->andReturn($filteredBilateralRequired1);
        $irhpPermitApplication1->shouldReceive('getBilateralFeePerPermit')
            ->with($applicationFeeType1StandardApplication, $applicationFeeType1StandardIssue)
            ->andReturn($feePerPermit1Standard);
        $irhpPermitApplication1->shouldReceive('getBilateralFeePerPermit')
            ->with($applicationFeeType1CabotageApplication, $applicationFeeType1CabotageIssue)
            ->andReturn($feePerPermit1Cabotage);
        $irhpPermitApplication1->shouldReceive('getBilateralFeeProductReference')
            ->with(IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED, IrhpPermitApplication::BILATERAL_APPLICATION_FEE_KEY)
            ->andReturn($productReference1StandardApplication);
        $irhpPermitApplication1->shouldReceive('getBilateralFeeProductReference')
            ->with(IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED, IrhpPermitApplication::BILATERAL_ISSUE_FEE_KEY)
            ->andReturn($productReference1StandardIssue);
        $irhpPermitApplication1->shouldReceive('getBilateralFeeProductReference')
            ->with(IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED, IrhpPermitApplication::BILATERAL_APPLICATION_FEE_KEY)
            ->andReturn($productReference1CabotageApplication);
        $irhpPermitApplication1->shouldReceive('getBilateralFeeProductReference')
            ->with(IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED, IrhpPermitApplication::BILATERAL_ISSUE_FEE_KEY)
            ->andReturn($productReference1CabotageIssue);

        $bilateralPermitUsageSelection2 = RefData::JOURNEY_MULTIPLE;
        $countryDesc2 = 'Norway';
        $filteredBilateralRequired2 = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 12,
        ];

        $productReference2StandardApplication = 'PRODUCT_REFERENCE_2_STANDARD_APPLICATION';
        $productReference2StandardIssue = 'PRODUCT_REFERENCE_2_STANDARD_ISSUE';

        $applicationFeeType2StandardApplication = m::mock(FeeType::class);
        $applicationFeeType2StandardIssue = m::mock(FeeType::class);
        $feePerPermit2Standard = 30;

        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with($productReference2StandardApplication)
            ->andReturn($applicationFeeType2StandardApplication);
        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with($productReference2StandardIssue)
            ->andReturn($applicationFeeType2StandardIssue);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($bilateralPermitUsageSelection2);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getCountry->getCountryDesc')
            ->withNoArgs()
            ->andReturn($countryDesc2);
        $irhpPermitApplication2->shouldReceive('getFilteredBilateralRequired')
            ->withNoArgs()
            ->andReturn($filteredBilateralRequired2);
        $irhpPermitApplication2->shouldReceive('getBilateralFeePerPermit')
            ->with($applicationFeeType2StandardApplication, $applicationFeeType2StandardIssue)
            ->andReturn($feePerPermit2Standard);
        $irhpPermitApplication2->shouldReceive('getBilateralFeeProductReference')
            ->with(IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED, IrhpPermitApplication::BILATERAL_APPLICATION_FEE_KEY)
            ->andReturn($productReference2StandardApplication);
        $irhpPermitApplication2->shouldReceive('getBilateralFeeProductReference')
            ->with(IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED, IrhpPermitApplication::BILATERAL_ISSUE_FEE_KEY)
            ->andReturn($productReference2StandardIssue);

        $irhpPermitApplications = [$irhpPermitApplication1, $irhpPermitApplication2];

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->withNoArgs()
            ->andReturn($irhpPermitApplications);

        $bilateralFeeBreakdownGenerator = new BilateralFeeBreakdownGenerator($feeTypeRepo);

        $expectedResult = [
            [
                'countryName' => 'Spain',
                'type' => 'permits.irhp.range.type.standard.single',
                'quantity' => 5,
                'total' => 225,
            ],
            [
                'countryName' => 'Spain',
                'type' => 'permits.irhp.range.type.cabotage.single',
                'quantity' => 8,
                'total' => 480,
            ],
            [
                'countryName' => 'Norway',
                'type' => 'permits.irhp.range.type.standard.multiple',
                'quantity' => 12,
                'total' => 360,
            ],
        ];

        $this->assertEquals(
            $expectedResult,
            $bilateralFeeBreakdownGenerator->generate($irhpApplication)
        );
    }
}
