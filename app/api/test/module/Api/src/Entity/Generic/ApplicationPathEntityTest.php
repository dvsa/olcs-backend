<?php

namespace Dvsa\OlcsTest\Api\Entity\Generic;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath as Entity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Mockery as m;

/**
 * ApplicationPath Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ApplicationPathEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @dataProvider dpGetAnswerValueByQuestionId
     */
    public function testGetAnswerValueByQuestionId($questionId, $expectedAnswerValue)
    {
        $applicationStep1 = m::mock(ApplicationStep::class);
        $applicationStep1->shouldReceive('getQuestion->getId')
            ->withNoArgs()
            ->andReturn(38);

        $applicationStep2 = m::mock(ApplicationStep::class);
        $applicationStep2->shouldReceive('getQuestion->getId')
            ->withNoArgs()
            ->andReturn(40);

        $applicationStep3 = m::mock(ApplicationStep::class);
        $applicationStep3->shouldReceive('getQuestion->getId')
            ->withNoArgs()
            ->andReturn(42);

        $applicationSteps = new ArrayCollection([$applicationStep1, $applicationStep2, $applicationStep3]);

        $qaEntity = m::mock(QaEntityInterface::class);
        $qaEntity->shouldReceive('getAnswer')
            ->with($applicationStep2)
            ->andReturn('answer value');

        $applicationPath = new Entity();
        $applicationPath->setApplicationSteps($applicationSteps);

        $this->assertEquals(
            $expectedAnswerValue,
            $applicationPath->getAnswerValueByQuestionId($questionId, $qaEntity)
        );
    }

    public function dpGetAnswerValueByQuestionId()
    {
        return [
            [40, 'answer value'],
            [62, null]
        ];
    }
}
