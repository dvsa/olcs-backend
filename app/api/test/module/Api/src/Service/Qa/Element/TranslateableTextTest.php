<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableTextFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * TranslateableTextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TranslateableTextTest extends MockeryTestCase
{
    private $key;

    private $parameter1;

    private $parameter2;

    private $translateableText;

    public function setUp()
    {
        $this->key = 'translateableTextKey';
        $this->parameter1 = 'parameter1';
        $this->parameter2 = 'parameter2';

        $this->translateableText = new TranslateableText($this->key);
        $this->translateableText->addParameter($this->parameter1);
        $this->translateableText->addParameter($this->parameter2);
    }

    public function testGetRepresentation()
    {
        $expectedRepresentation = [
            'key' => $this->key,
            'parameters' => [
                $this->parameter1,
                $this->parameter2
            ]
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $this->translateableText->getRepresentation()
        );
    }

    public function testSetParameter()
    {
        $newParameter2 = 'newParameter2';

        $expectedRepresentation = [
            'key' => $this->key,
            'parameters' => [
                $this->parameter1,
                $newParameter2
            ]
        ];

        $this->translateableText->setParameter(1, $newParameter2);

        $this->assertEquals(
            $expectedRepresentation,
            $this->translateableText->getRepresentation()
        );
    }

    public function testSetParameterBadIndex()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No parameter exists at index 4');

        $this->translateableText->setParameter(4, 'foo');
    }
}
