<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\EmissionsCategory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\EmissionsCategoryConditionalAdder;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\EmissionsCategoryFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermits;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * EmissionsCategoryConditionalAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EmissionsCategoryConditionalAdderTest extends MockeryTestCase
{
    private $emissionsCategoryFactory;

    private $irhpPermitRangeRepo;

    private $irhpPermitRepo;

    private $emissionsCategoryConditionalAdder;

    private $noOfPermits;

    private $fieldName;

    private $labelTranslationKey;

    private $value;

    private $emissionsCategoryId;

    private $stockId;

    public function setUp()
    {
        $this->emissionsCategoryFactory = m::mock(EmissionsCategoryFactory::class);
        $this->irhpPermitRangeRepo = m::mock(IrhpPermitRangeRepository::class);
        $this->irhpPermitRepo = m::mock(IrhpPermitRepository::class);

        $this->emissionsCategoryConditionalAdder = new EmissionsCategoryConditionalAdder(
            $this->emissionsCategoryFactory,
            $this->irhpPermitRangeRepo,
            $this->irhpPermitRepo
        );

        $this->noOfPermits = m::mock(NoOfPermits::class);
        $this->fieldName = 'euro5Required';
        $this->labelTranslationKey = 'qanda.ecmt-short-term.number-of-permits.label.euro5';
        $this->value = '45';
        $this->emissionsCategoryId = RefData::EMISSIONS_CATEGORY_EURO5_REF;
        $this->stockId = 28;
    }

    public function testAddWhenRangesExistAndFreePermits()
    {
        $combinedRangeSize = 100;
        $permitCount = 97;
        $permitsRemaining = 3;

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($this->stockId, $this->emissionsCategoryId)
            ->andReturn($combinedRangeSize);

        $this->irhpPermitRepo->shouldReceive('getPermitCount')
            ->with($this->stockId, $this->emissionsCategoryId)
            ->andReturn($permitCount);

        $emissionsCategory = m::mock(EmissionsCategory::class);

        $this->emissionsCategoryFactory->shouldReceive('create')
            ->with($this->fieldName, $this->labelTranslationKey, $this->value, $permitsRemaining)
            ->once()
            ->andReturn($emissionsCategory);

        $this->noOfPermits->shouldReceive('addEmissionsCategory')
            ->with($emissionsCategory)
            ->once();

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $this->noOfPermits,
            $this->fieldName,
            $this->labelTranslationKey,
            $this->value,
            $this->emissionsCategoryId,
            $this->stockId
        );
    }

    public function testNoAddWhenNoRanges()
    {
        $combinedRangeSize = null;

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($this->stockId, $this->emissionsCategoryId)
            ->andReturn($combinedRangeSize);

        $this->noOfPermits->shouldReceive('addEmissionsCategory')
            ->never();

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $this->noOfPermits,
            $this->fieldName,
            $this->labelTranslationKey,
            $this->value,
            $this->emissionsCategoryId,
            $this->stockId
        );
    }

    public function testNoAddWhenNoFreePermitsInRanges()
    {
        $combinedRangeSize = 100;
        $permitCount = 100;

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($this->stockId, $this->emissionsCategoryId)
            ->andReturn($combinedRangeSize);

        $this->irhpPermitRepo->shouldReceive('getPermitCount')
            ->with($this->stockId, $this->emissionsCategoryId)
            ->andReturn($permitCount);

        $this->noOfPermits->shouldReceive('addEmissionsCategory')
            ->never();

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $this->noOfPermits,
            $this->fieldName,
            $this->labelTranslationKey,
            $this->value,
            $this->emissionsCategoryId,
            $this->stockId
        );
    }
}
