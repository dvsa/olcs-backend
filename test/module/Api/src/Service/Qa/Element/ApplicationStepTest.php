<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\ValidatorList;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApplicationStepTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStepTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $type = 'checkbox';
        $fieldsetName = 'fields123';
        $elementRepresentation = ['elementRepresentation'];
        $validatorListRepresentation = ['validatorListRepresentation'];

        $element = m::mock(ElementInterface::class);
        $element->shouldReceive('getRepresentation')
            ->andReturn($elementRepresentation);

        $validatorList = m::mock(ValidatorList::class);
        $validatorList->shouldReceive('getRepresentation')
            ->andReturn($validatorListRepresentation);

        $applicationStep = new ApplicationStep($type, $fieldsetName, $element, $validatorList);

        $expectedRepresentation = [
            'type' => $type,
            'fieldsetName' => $fieldsetName,
            'element' => $elementRepresentation,
            'validators' => $validatorListRepresentation
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $applicationStep->getRepresentation()
        );
    }
}
