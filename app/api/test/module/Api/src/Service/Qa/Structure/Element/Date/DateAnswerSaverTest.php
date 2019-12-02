<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question as QuestionEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Common\DateTimeFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\DateAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * DateAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateAnswerSaverTest extends MockeryTestCase
{
    public function testSave()
    {
        $dateArray = [
            'year' => '2020',
            'month' => '04',
            'day' => '15'
        ];

        $postData = [
            'fieldset13' => [
                'qaElement' => $dateArray
            ]
        ];

        $applicationStep = m::mock(ApplicationStepEntity::class);
        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $answerValue = m::mock(DateTime::class);

        $dateTimeFactory = m::mock(DateTimeFactory::class);
        $dateTimeFactory->shouldReceive('create')
            ->with('2020-04-15')
            ->once()
            ->andReturn($answerValue);

        $genericAnswerFetcher = m::mock(GenericAnswerFetcher::class);
        $genericAnswerFetcher->shouldReceive('fetch')
            ->with($applicationStep, $postData)
            ->andReturn($dateArray);

        $genericAnswerWriter = m::mock(GenericAnswerWriter::class);
        $genericAnswerWriter->shouldReceive('write')
            ->with($applicationStep, $irhpApplication, $answerValue, QuestionEntity::QUESTION_TYPE_DATE)
            ->once();

        $dateAnswerSaver = new DateAnswerSaver($genericAnswerWriter, $genericAnswerFetcher, $dateTimeFactory);
        $dateAnswerSaver->save($applicationStep, $irhpApplication, $postData);
    }
}
