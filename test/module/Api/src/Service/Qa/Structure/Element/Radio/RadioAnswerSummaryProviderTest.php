<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\RadioAnswerSummaryProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * RadioAnswerSummaryProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RadioAnswerSummaryProviderTest extends MockeryTestCase
{
    private $applicationStepEntity;

    private $qaContext;

    private $element;

    private $optionsGenerator;

    private $radioAnswerSummaryProvider;

    public function setUp(): void
    {
        $decodedOptionSourceSource = [
            'name' => 'database',
            'tableName' => 'items'
        ];

        $decodedOptionSource = [
            'source' => $decodedOptionSourceSource
        ];

        $radioOptions = [
            [
                'value' => 'item1Value',
                'label' => 'item1Label'
            ],
            [
                'value' => 'item2Value',
                'label' => 'item2Label'
            ],
            [
                'value' => 'item3Value',
                'label' => 'item3Label'
            ],
        ];

        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->withNoArgs()
            ->andReturn($decodedOptionSource);

        $this->qaContext = m::mock(QaContext::class);
        $this->qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($this->applicationStepEntity);

        $this->element = m::mock(ElementInterface::class);

        $this->optionsGenerator = m::mock(OptionsGenerator::class);
        $this->optionsGenerator->shouldReceive('generate')
            ->with($decodedOptionSourceSource)
            ->andReturn($radioOptions);

        $this->radioAnswerSummaryProvider = new RadioAnswerSummaryProvider($this->optionsGenerator);
    }

    public function testGetTemplateName()
    {
        $this->assertEquals(
            'generic',
            $this->radioAnswerSummaryProvider->getTemplateName()
        );
    }

    /**
     * @dataProvider dpSnapshot
     */
    public function testGetTemplateVariables($isSnapshot)
    {
        $qaAnswer = 'item2Value';

        $this->qaContext->shouldReceive('getAnswerValue')
            ->withNoArgs()
            ->andReturn($qaAnswer);

        $templateVariables = $this->radioAnswerSummaryProvider->getTemplateVariables(
            $this->qaContext,
            $this->element,
            $isSnapshot
        );

        $this->assertEquals(
            ['answer' => 'item2Label'],
            $templateVariables
        );
    }

    /**
     * @dataProvider dpSnapshot
     */
    public function testGetTemplateVariablesException($isSnapshot)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Answer not found in list of options');

        $qaAnswer = 'item4Value';

        $this->qaContext->shouldReceive('getAnswerValue')
            ->withNoArgs()
            ->andReturn($qaAnswer);

        $this->radioAnswerSummaryProvider->getTemplateVariables($this->qaContext, $this->element, $isSnapshot);
    }

    public function dpSnapshot()
    {
        return [
            [true],
            [false]
        ];
    }
}
