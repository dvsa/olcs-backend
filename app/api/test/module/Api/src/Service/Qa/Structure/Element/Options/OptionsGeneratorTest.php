<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\SourceInterface;
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

    private $optionsGenerator;

    public function setUp()
    {
        $this->directSource = m::mock(SourceInterface::class);
        
        $this->optionsGenerator = new OptionsGenerator();
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

        $this->applicationStep = m::mock(ApplicationStep::class);
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
            '1' => 'Food',
            '2' => 'Minerals',
            '3' => 'Metal'
        ];

        $this->directSource->shouldReceive('generateOptions')
            ->with($sourceOptions)
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
