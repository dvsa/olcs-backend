<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermits;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsText;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsTextFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsGeneratorTest extends MockeryTestCase
{
    private $irhpPermitApplication;

    private $elementGeneratorContext;

    private $noOfPermits;

    private $noOfPermitsTextFactory;

    private $noOfPermitsGenerator;

    public function setUp()
    {
        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $this->elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $this->elementGeneratorContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);

        $this->noOfPermits = m::mock(NoOfPermits::class);

        $noOfPermitsFactory = m::mock(NoOfPermitsFactory::class);
        $noOfPermitsFactory->shouldReceive('create')
            ->withNoArgs()
            ->andReturn($this->noOfPermits);

        $this->noOfPermitsTextFactory = m::mock(NoOfPermitsTextFactory::class);

        $this->noOfPermitsGenerator = new NoOfPermitsGenerator($noOfPermitsFactory, $this->noOfPermitsTextFactory);
    }

    /**
     * @dataProvider dpGenerateOneTextbox
     */
    public function testGenerateOneTextbox(
        $permitUsageSelection,
        $cabotageSelection,
        $isAssociatedWithBilateralOnlyApplicationPathGroup,
        $required,
        $expectedText
    ) {
        $this->irhpPermitApplication->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($permitUsageSelection);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn($cabotageSelection);
        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($required);
        $this->irhpPermitApplication->shouldReceive('isAssociatedWithBilateralOnlyApplicationPathGroup')
            ->withNoArgs()
            ->andReturn($isAssociatedWithBilateralOnlyApplicationPathGroup);

        $noOfPermitsText = m::mock(NoOfPermitsText::class);
        $this->noOfPermitsTextFactory->shouldReceive('create')
            ->with($expectedText['name'], $expectedText['label'], $expectedText['hint'], $expectedText['value'])
            ->andReturn($noOfPermitsText);

        $this->noOfPermits->shouldReceive('addText')
            ->with($noOfPermitsText)
            ->once();

        $this->assertSame(
            $this->noOfPermits,
            $this->noOfPermitsGenerator->generate($this->elementGeneratorContext)
        );
    }

    public function dpGenerateOneTextbox()
    {
        return [
            'single, standard only, not associated with bilateral only application group' => [
                'permitUsageSelection' => RefData::JOURNEY_SINGLE,
                'cabotageSelection' => Answer::BILATERAL_CABOTAGE_ONLY,
                'isAssociatedWithBilateralOnlyApplicationPathGroup' => false,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 7,
                ],
                'expectedText' => [
                    'name' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.single',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.single',
                    'value' => 7,
                ]
            ],
            'single, cabotage only, associated with bilateral only application group' => [
                'permitUsageSelection' => RefData::JOURNEY_SINGLE,
                'cabotageSelection' => Answer::BILATERAL_CABOTAGE_ONLY,
                'isAssociatedWithBilateralOnlyApplicationPathGroup' => true,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 6,
                ],
                'expectedText' => [
                    'name' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.pre-october-2021',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.single',
                    'value' => 6,
                ]
            ],
            'single, cabotage only, not associated with bilateral only application group' => [
                'permitUsageSelection' => RefData::JOURNEY_SINGLE,
                'cabotageSelection' => Answer::BILATERAL_CABOTAGE_ONLY,
                'isAssociatedWithBilateralOnlyApplicationPathGroup' => false,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 8,
                ],
                'expectedText' => [
                    'name' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.single',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.single',
                    'value' => 8,
                ]
            ],
            'multiple, standard only, not associated with bilateral only application group' => [
                'permitUsageSelection' => RefData::JOURNEY_MULTIPLE,
                'cabotageSelection' => Answer::BILATERAL_CABOTAGE_ONLY,
                'isAssociatedWithBilateralOnlyApplicationPathGroup' => false,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 7,
                ],
                'expectedText' => [
                    'name' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.multiple',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.multiple',
                    'value' => 7,
                ]
            ],
            'multiple, cabotage only, associated with bilateral only application group' => [
                'permitUsageSelection' => RefData::JOURNEY_MULTIPLE,
                'cabotageSelection' => Answer::BILATERAL_CABOTAGE_ONLY,
                'isAssociatedWithBilateralOnlyApplicationPathGroup' => true,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 6,
                ],
                'expectedText' => [
                    'name' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.pre-october-2021',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.multiple',
                    'value' => 6,
                ]
            ],
            'multiple, cabotage only, not associated with bilateral only application group' => [
                'permitUsageSelection' => RefData::JOURNEY_MULTIPLE,
                'cabotageSelection' => Answer::BILATERAL_CABOTAGE_ONLY,
                'isAssociatedWithBilateralOnlyApplicationPathGroup' => false,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 8,
                ],
                'expectedText' => [
                    'name' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.multiple',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.multiple',
                    'value' => 8,
                ]
            ],
        ];
    }

    /**
     * @dataProvider dpGenerateTwoTextboxes
     */
    public function testGenerateTwoTextboxes($permitUsageSelection, $required, $expectedText1, $expectedText2)
    {
        $this->irhpPermitApplication->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($permitUsageSelection);
        $this->irhpPermitApplication->shouldReceive('getBilateralCabotageSelection')
            ->withNoArgs()
            ->andReturn(Answer::BILATERAL_STANDARD_AND_CABOTAGE);
        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($required);
        $this->irhpPermitApplication->shouldReceive('isAssociatedWithBilateralOnlyApplicationPathGroup')
            ->withNoArgs()
            ->andReturn(false);

        $noOfPermitsText1 = m::mock(NoOfPermitsText::class);
        $this->noOfPermitsTextFactory->shouldReceive('create')
            ->with(
                IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                $expectedText1['label'],
                $expectedText1['hint'],
                $expectedText1['value']
            )
            ->andReturn($noOfPermitsText1);

        $noOfPermitsText2 = m::mock(NoOfPermitsText::class);
        $this->noOfPermitsTextFactory->shouldReceive('create')
            ->with(
                IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                $expectedText2['label'],
                $expectedText2['hint'],
                $expectedText2['value']
            )
            ->andReturn($noOfPermitsText2);

        $this->noOfPermits->shouldReceive('addText')
            ->with($noOfPermitsText1)
            ->once()
            ->ordered();
        $this->noOfPermits->shouldReceive('addText')
            ->with($noOfPermitsText2)
            ->once()
            ->ordered();

        $this->assertSame(
            $this->noOfPermits,
            $this->noOfPermitsGenerator->generate($this->elementGeneratorContext)
        );
    }

    public function dpGenerateTwoTextboxes()
    {
        return [
            'single' => [
                'permitUsageSelection' => RefData::JOURNEY_SINGLE,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 3,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 4,
                ],
                'expectedText1' => [
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.standard.single',
                    'label' => 'qanda.bilaterals.number-of-permits.label.standard.single',
                    'value' => 3,
                ],
                'expectedText2' => [
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.single',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.single',
                    'value' => 4,
                ],
            ],
            'multiple' => [
                'permitUsageSelection' => RefData::JOURNEY_MULTIPLE,
                'required' => [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 5,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 6,
                ],
                'expectedText1' => [
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.standard.multiple',
                    'label' => 'qanda.bilaterals.number-of-permits.label.standard.multiple',
                    'value' => 5,
                ],
                'expectedText2' => [
                    'hint' => 'qanda.bilaterals.number-of-permits.hint.cabotage.multiple',
                    'label' => 'qanda.bilaterals.number-of-permits.label.cabotage.multiple',
                    'value' => 6,
                ],
            ],
        ];
    }
}
