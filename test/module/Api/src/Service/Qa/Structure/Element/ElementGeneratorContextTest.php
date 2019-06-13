<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ElementGeneratorContextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ElementGeneratorContextTest extends MockeryTestCase
{
    private $validatorList;

    private $applicationStepEntity;

    private $irhpApplicationEntity;

    private $elementGeneratorContext;

    public function setUp()
    {
        $this->validatorList = m::mock(ValidatorList::class);

        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->elementGeneratorContext = new ElementGeneratorContext(
            $this->validatorList,
            $this->applicationStepEntity,
            $this->irhpApplicationEntity
        );
    }

    public function testGetValidatorTest()
    {
        $this->assertSame(
            $this->validatorList,
            $this->elementGeneratorContext->getValidatorList()
        );
    }

    public function testGetApplicationStepEntity()
    {
        $this->assertSame(
            $this->applicationStepEntity,
            $this->elementGeneratorContext->getApplicationStepEntity()
        );
    }

    public function testGetIrhpApplicationEntity()
    {
        $this->assertSame(
            $this->irhpApplicationEntity,
            $this->elementGeneratorContext->getIrhpApplicationEntity()
        );
    }
}
