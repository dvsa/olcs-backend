<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerSummaryProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * GenericAnswerSummaryProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerSummaryProviderTest extends MockeryTestCase
{
    private $genericAnswerSummaryProvider;

    public function setUp()
    {
        $this->genericAnswerSummaryProvider = new GenericAnswerSummaryProvider();
    }

    public function testGetTemplateName()
    {
        $this->assertEquals(
            'generic',
            $this->genericAnswerSummaryProvider->getTemplateName()
        );
    }

    /**
     * @dataProvider dpSnapshot
     */
    public function testGetTemplateVariables($isSnapshot)
    {
        $answerValue = 'foo';

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getAnswer')
            ->with($applicationStepEntity)
            ->andReturn($answerValue);

        $templateVariables = $this->genericAnswerSummaryProvider->getTemplateVariables(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $isSnapshot
        );

        $this->assertEquals(
            ['answer' => $answerValue],
            $templateVariables
        );
    }

    public function dpSnapshot()
    {
        return [
            [true],
            [false]
        ];
    }
}
