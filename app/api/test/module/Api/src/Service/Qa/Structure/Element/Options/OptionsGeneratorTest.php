<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\SourceInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionList;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionListFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * OptionsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OptionsGeneratorTest extends MockeryTestCase
{
    private $directSource;

    private $optionListFactory;

    private $optionFactory;

    private $optionsGenerator;

    public function setUp()
    {
        $this->directSource = m::mock(SourceInterface::class);

        $this->optionListFactory = m::mock(OptionListFactory::class);

        $this->optionFactory = m::mock(OptionFactory::class);
        
        $this->optionsGenerator = new OptionsGenerator($this->optionListFactory, $this->optionFactory);

        $this->optionsGenerator->registerSource(
            'refData',
            m::mock(SourceInterface::class)
        );
        $this->optionsGenerator->registerSource(
            'direct',
            $this->directSource
        );
        $this->optionsGenerator->registerSource(
            'other',
            m::mock(SourceInterface::class)
        );
    }

    public function testGenerate()
    {
        $sourceName = 'direct';
        $sourceOptions = [
            'option1' => 'value1',
            'option2' => 'value2'
        ];

        $data = [
            'name' => $sourceName,
            'options' => $sourceOptions
        ];

        $returnedOptions = [
            [
                'value' => '1',
                'label' => 'Food',
                'hint' => 'Hint for Food option',
            ],
            [
                'value' => '2',
                'label' => 'Minerals',
                'hint' => 'Hint for Minerals option',
            ]
        ];


        $optionList = m::mock(OptionList::class);

        $this->optionListFactory->shouldReceive('create')
            ->with($this->optionFactory)
            ->once()
            ->andReturn($optionList);

        $this->directSource->shouldReceive('populateOptionList')
            ->with($optionList, $sourceOptions)
            ->once()
            ->globally()
            ->ordered();

        $optionList->shouldReceive('getRepresentation')
            ->once()
            ->globally()
            ->ordered()
            ->andReturn($returnedOptions);

        $this->assertEquals(
            $returnedOptions,
            $this->optionsGenerator->generate($data)
        );
    }

    public function testExceptionOnUnknownSourceName()
    {
        $data = [
            'name' => 'test',
            'options' => [
                'key1' => 'value1',
                'key2' => 'value2'
            ]
        ];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No source found for name test');

        $this->optionsGenerator->generate($data);
    }
}
