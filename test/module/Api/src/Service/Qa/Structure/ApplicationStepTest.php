<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApplicationStepTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStepTest extends MockeryTestCase
{
    private $type;

    private $fieldsetName;

    private $elementRepresentation;

    private $validatorListRepresentation;

    private $validatorList;

    private $applicationStep;

    public function setUp()
    {
        $this->type = 'checkbox';
        $this->fieldsetName = 'fields123';
        $this->elementRepresentation = ['elementRepresentation'];
        $this->validatorListRepresentation = ['validatorListRepresentation'];

        $element = m::mock(ElementInterface::class);
        $element->shouldReceive('getRepresentation')
            ->andReturn($this->elementRepresentation);

        $this->validatorList = m::mock(ValidatorList::class);
        $this->validatorList->shouldReceive('getRepresentation')
            ->andReturn($this->validatorListRepresentation);

        $this->applicationStep = new ApplicationStep($this->type, $this->fieldsetName, $element, $this->validatorList);
    }

    public function testGetRepresentation()
    {
        $expectedRepresentation = [
            'type' => $this->type,
            'fieldsetName' => $this->fieldsetName,
            'element' => $this->elementRepresentation,
            'validators' => $this->validatorListRepresentation
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $this->applicationStep->getRepresentation()
        );
    }

    public function testGetValidatorList()
    {
        $this->assertSame(
            $this->validatorList,
            $this->applicationStep->getValidatorList()
        );
    }
}
