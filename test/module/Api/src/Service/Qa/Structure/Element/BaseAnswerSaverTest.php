<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\BaseAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * BaseAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class BaseAnswerSaverTest extends MockeryTestCase
{
    private $qaElementValue = 'qaElementValue';

    private $postData;

    private $applicationStep;

    private $irhpApplication;

    private $genericAnswerWriter;

    private $baseAnswerSaver;

    public function setUp()
    {
        $fieldsetName = 'fields456';

        $this->postData = [
            $fieldsetName => [
                'qaElement' => 'qaElementValue'
            ]
        ];

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getFieldsetName')
            ->andReturn($fieldsetName);

        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $genericAnswerFetcher = m::mock(GenericAnswerFetcher::class);
        $genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData)
            ->andReturn($this->qaElementValue);

        $this->baseAnswerSaver = new BaseAnswerSaver($this->genericAnswerWriter, $genericAnswerFetcher);
    }

    public function testSaveWithoutQuestionType()
    {
        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->applicationStep, $this->irhpApplication, $this->qaElementValue, null)
            ->once();

        $this->baseAnswerSaver->save($this->applicationStep, $this->irhpApplication, $this->postData);
    }

    /**
     * @dataProvider dpSaveWithQuestionType
     */
    public function testSaveWithQuestionType($questionType)
    {
        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->applicationStep, $this->irhpApplication, $this->qaElementValue, $questionType)
            ->once();

        $this->baseAnswerSaver->save($this->applicationStep, $this->irhpApplication, $this->postData, $questionType);
    }

    public function dpSaveWithQuestionType()
    {
        return [
            [Question::QUESTION_TYPE_STRING],
            [Question::QUESTION_TYPE_INTEGER],
            [Question::QUESTION_TYPE_BOOLEAN],
        ];
    }
}
