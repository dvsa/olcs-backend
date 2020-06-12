<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
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
        $qaContext = m::mock(QaContext::class);
        $qaContext->shouldReceive('getAnswerValue')
            ->withNoArgs()
            ->andReturn($qaAnswer);

        $templateVariables = $this->checkboxAnswerSummaryProvider->getTemplateVariables($qaContext, $isSnapshot);

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
