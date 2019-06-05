<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TranslateableTextGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TranslateableTextGeneratorTest extends MockeryTestCase
{
    private $optionsKey;

    private $translateableText;

    private $translateableTextFactory;

    private $translateableTextGenerator;

    public function setUp()
    {
        $this->optionsKey = 'optionsKey';

        $this->translateableText = m::mock(TranslateableText::class);

        $this->translateableTextFactory = m::mock(TranslateableTextFactory::class);
        $this->translateableTextFactory->shouldReceive('create')
            ->with($this->optionsKey)
            ->andReturn($this->translateableText);

        $this->translateableTextGenerator = new TranslateableTextGenerator($this->translateableTextFactory);
    }

    public function testGenerateWithNoParameters()
    {
        $options = [
            'key' => $this->optionsKey
        ];

        $this->assertSame(
            $this->translateableText,
            $this->translateableTextGenerator->generate($options)
        );
    }

    public function testGenerateWithParameters()
    {
        $parameter1 = 'parameter1';
        $parameter2 = 'parameter2';

        $options = [
            'key' => $this->optionsKey,
            'parameters' => [
                $parameter1,
                $parameter2
            ]
        ];

        $this->translateableText->shouldReceive('addParameter')
            ->with($parameter1)
            ->once()
            ->ordered();
        $this->translateableText->shouldReceive('addParameter')
            ->with($parameter2)
            ->once()
            ->ordered();

        $this->assertSame(
            $this->translateableText,
            $this->translateableTextGenerator->generate($options)
        );
    }
}
