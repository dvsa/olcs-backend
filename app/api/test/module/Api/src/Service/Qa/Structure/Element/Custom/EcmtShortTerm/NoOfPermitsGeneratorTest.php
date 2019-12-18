<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\EmissionsCategoryConditionalAdder;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\FieldNames;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermits;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsGenerator;
use RuntimeException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsGeneratorTest extends MockeryTestCase
{
    private $irhpApplication;

    private $elementGeneratorContext;

    private $noOfPermitsFactory;

    private $emissionsCategoryConditionalAdder;

    private $noOfPermitsGenerator;

    public function setUp()
    {
        $this->irhpApplication = m::mock(IrhpApplicationEntity::class);

        $this->elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $this->elementGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($this->irhpApplication);

        $this->noOfPermitsFactory = m::mock(NoOfPermitsFactory::class);

        $this->emissionsCategoryConditionalAdder = m::mock(EmissionsCategoryConditionalAdder::class);

        $this->noOfPermitsGenerator = new NoOfPermitsGenerator(
            $this->noOfPermitsFactory,
            $this->emissionsCategoryConditionalAdder
        );
    }

    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate($irhpPermitTypeId, $expectedMaxPermitted)
    {
        $stockId = 22;
        $validityYear = 2015;
        $totAuthVehicles = 12;
        $requiredEuro5 = 13;
        $requiredEuro6 = 7;

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($stockId);
        $irhpPermitStock->shouldReceive('getValidityYear')
            ->withNoArgs()
            ->andReturn($validityYear);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->withNoArgs()
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->withNoArgs()
            ->andReturn($requiredEuro5);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->withNoArgs()
            ->andReturn($requiredEuro6);

        $this->irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);
        $this->irhpApplication->shouldReceive('getLicence->getTotAuthVehicles')
            ->withNoArgs()
            ->andReturn($totAuthVehicles);
        $this->irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $noOfPermits = m::mock(NoOfPermits::class);

        $this->noOfPermitsFactory->shouldReceive('create')
            ->with($validityYear, $expectedMaxPermitted)
            ->once()
            ->andReturn($noOfPermits);

        $this->emissionsCategoryConditionalAdder->shouldReceive('addIfRequired')
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
        $this->emissionsCategoryConditionalAdder->shouldReceive('addIfRequired')
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

        $this->assertSame(
            $noOfPermits,
            $this->noOfPermitsGenerator->generate($this->elementGeneratorContext)
        );
    }

    public function dpGenerate()
    {
        return [
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT, 12],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, 24],
        ];
    }

    /**
     * @dataProvider dpGenerateExceptionOnUnsupportedType
     */
    public function testGenerateExceptionOnUnsupportedType($irhpPermitTypeId)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This question does not support permit type ' . $irhpPermitTypeId);

        $this->irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $this->noOfPermitsGenerator->generate($this->elementGeneratorContext);
    }

    public function dpGenerateExceptionOnUnsupportedType()
    {
        return [
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER],
        ];
    }
}
