<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\FilteredTranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * FilteredTranslateableTextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FilteredTranslateableTextTest extends MockeryTestCase
{
    private $filter;

    private $translateableText;

    private $filteredTranslateableText;

    public function setUp()
    {
        $this->filter = 'htmlEscape';

        $this->translateableText = m::mock(TranslateableText::class);

        $this->filteredTranslateableText = new FilteredTranslateableText($this->filter, $this->translateableText);
    }

    public function testGetRepresentation()
    {
        $translateableTextRepresentation = [
            'key' => 'translateableTextKey',
            'parameters' => [
                'translateableTextParameter1',
                'translateableTextParameter2'
            ]
        ];

        $filteredTranslateableTextRepresentation = [
            'filter' => $this->filter,
            'translateableText' => $translateableTextRepresentation
        ];

        $this->translateableText->shouldReceive('getRepresentation')
            ->andReturn($translateableTextRepresentation);

        $this->assertEquals(
            $filteredTranslateableTextRepresentation,
            $this->filteredTranslateableText->getRepresentation()
        );
    }

    public function testGetTranslateableText()
    {
        $this->assertEquals(
            $this->translateableText,
            $this->filteredTranslateableText->getTranslateableText()
        );
    }
}
