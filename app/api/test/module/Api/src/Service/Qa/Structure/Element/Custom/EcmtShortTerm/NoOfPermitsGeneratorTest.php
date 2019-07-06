<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\EmissionsCategoryConditionalAdder;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\FieldNames;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermits;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $stockId = 22;
        $validityYear = 2015;
        $totAuthVehicles = 12;
        $requiredEuro5 = 13;
        $requiredEuro6 = 7;

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('getId')
            ->andReturn($stockId);
        $irhpPermitStock->shouldReceive('getValidityYear')
            ->andReturn($validityYear);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn($requiredEuro5);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn($requiredEuro6);

        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $irhpApplication->shouldReceive('getLicence->getTotAuthVehicles')
            ->andReturn($totAuthVehicles);

        $noOfPermits = m::mock(NoOfPermits::class);

        $noOfPermitsFactory = m::mock(NoOfPermitsFactory::class);
        $noOfPermitsFactory->shouldReceive('create')
            ->with($validityYear, $totAuthVehicles)
            ->once()
            ->andReturn($noOfPermits);

        $emissionsCategoryConditionalAdder = m::mock(EmissionsCategoryConditionalAdder::class);
        $emissionsCategoryConditionalAdder->shouldReceive('addIfRequired')
            ->with(
                $noOfPermits,
                FieldNames::REQUIRED_EURO5,
                'qanda.ecmt-short-term.number-of-permits.label.euro5',
                $requiredEuro5,
                RefData::EMISSIONS_CATEGORY_EURO5_REF,
                $stockId
            )
            ->once()
            ->ordered();
        $emissionsCategoryConditionalAdder->shouldReceive('addIfRequired')
            ->with(
                $noOfPermits,
                FieldNames::REQUIRED_EURO6,
                'qanda.ecmt-short-term.number-of-permits.label.euro6',
                $requiredEuro6,
                RefData::EMISSIONS_CATEGORY_EURO6_REF,
                $stockId
            )
            ->once()
            ->ordered();

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $elementGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($irhpApplication);

        $noOfPermitsGenerator = new NoOfPermitsGenerator($noOfPermitsFactory, $emissionsCategoryConditionalAdder);

        $this->assertSame(
            $noOfPermits,
            $noOfPermitsGenerator->generate($elementGeneratorContext)
        );
    }
}
