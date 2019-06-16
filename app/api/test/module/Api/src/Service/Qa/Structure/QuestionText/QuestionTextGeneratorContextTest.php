<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * QuestionTextGeneratorContextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QuestionTextGeneratorContextTest extends MockeryTestCase
{
    private $applicationStepEntity;

    private $irhpApplicationEntity;

    private $questionTextGeneratorContext;

    public function setUp()
    {
        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->questionTextGeneratorContext = new QuestionTextGeneratorContext(
            $this->applicationStepEntity,
            $this->irhpApplicationEntity
        );
    }

    public function testGetApplicationStepEntity()
    {
        $this->assertSame(
            $this->applicationStepEntity,
            $this->questionTextGeneratorContext->getApplicationStepEntity()
        );
    }

    public function testGetIrhpApplicationEntity()
    {
        $this->assertSame(
            $this->irhpApplicationEntity,
            $this->questionTextGeneratorContext->getIrhpApplicationEntity()
        );
    }
}
