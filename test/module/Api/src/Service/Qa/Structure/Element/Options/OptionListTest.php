<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\Option;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionList;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * OptionListTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OptionListTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $item1Value = '1';
        $item1Label = 'Food';
        $item1Hint = 'Hint for the Food item';

        $item2Value = '3';
        $item2Label = 'Metals';
        $item2Hint = 'Hint for the Metals item';

        $option1Representation = [
            'value' => $item1Value,
            'label' => $item1Label,
            'hint' => $item1Hint,
        ];

        $option2Representation = [
            'value' => $item2Value,
            'label' => $item2Label,
            'hint' => $item2Hint,
        ];

        $option1 = m::mock(Option::class);
        $option1->shouldReceive('getRepresentation')
            ->andReturn($option1Representation);
        
        $option2 = m::mock(Option::class);
        $option2->shouldReceive('getRepresentation')
            ->andReturn($option2Representation);

        $optionFactory = m::mock(OptionFactory::class);
        $optionFactory->shouldReceive('create')
            ->with($item1Value, $item1Label, $item1Hint)
            ->once()
            ->andReturn($option1);
        $optionFactory->shouldReceive('create')
            ->with($item2Value, $item2Label, $item2Hint)
            ->once()
            ->andReturn($option2);

        $expectedRepresentation = [
            $option1Representation,
            $option2Representation,
        ];

        $optionList = new OptionList($optionFactory);
        $optionList->add($item1Value, $item1Label, $item1Hint);
        $optionList->add($item2Value, $item2Label, $item2Hint);

        $this->assertEquals(
            $expectedRepresentation,
            $optionList->getRepresentation()
        );
    }
}
