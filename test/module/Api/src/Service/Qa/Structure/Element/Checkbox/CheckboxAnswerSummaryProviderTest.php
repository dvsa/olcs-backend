<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox\CheckboxAnswerSummaryProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CheckboxAnswerSummaryProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckboxAnswerSummaryProviderTest extends MockeryTestCase
{
    private $checkboxAnswerSummaryProvider;

    public function setUp()
    {
        $this->checkboxAnswerSummaryProvider = new CheckboxAnswerSummaryProvider();
    }

    public function testGetTemplateName()
    {
        $this->assertEquals(
            'generic',
            $this->checkboxAnswerSummaryProvider->getTemplateName()
        );
    }

    /**
     * @dataProvider dpGetTemplateVariables
     */
    public function testGetTemplateVariables($isSnapshot, $qaAnswer, $expectedAnswerValue)
    {
        $applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getAnswer')
            ->with($applicationStepEntity)
            ->andReturn($qaAnswer);

        $templateVariables = $this->checkboxAnswerSummaryProvider->getTemplateVariables(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $isSnapshot
        );

        $this->assertEquals(
            ['answer' => $expectedAnswerValue],
            $templateVariables
        );
    }

    public function dpGetTemplateVariables()
    {
        return [
            [true, true, 'Yes'],
            [false, true, 'Yes'],
            [true, false, 'No'],
            [false, false, 'No'],
        ];
    }
}
