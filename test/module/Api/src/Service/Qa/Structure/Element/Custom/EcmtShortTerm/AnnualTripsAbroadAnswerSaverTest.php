<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question as QuestionEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\AnnualTripsAbroadAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * AnnualTripsAbroadAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnnualTripsAbroadAnswerSaverTest extends MockeryTestCase
{
    public function testSave()
    {
        $postData = [
            'fieldset68' => [
                'qaElement' => '14'
            ]
        ];

        $answer = '14';

        $applicationStep = m::mock(ApplicationStepEntity::class);

        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $genericAnswerFetcher = m::mock(GenericAnswerFetcher::class);
        $genericAnswerFetcher->shouldReceive('fetch')
            ->with($applicationStep, $postData)
            ->andReturn($answer);

        $genericAnswerWriter = m::mock(GenericAnswerWriter::class);
        $genericAnswerWriter->shouldReceive('write')
            ->with($applicationStep, $irhpApplication, $answer, QuestionEntity::QUESTION_TYPE_STRING)
            ->once();

        $annualTripsAbroadAnswerSaver = new AnnualTripsAbroadAnswerSaver($genericAnswerFetcher, $genericAnswerWriter);
        $annualTripsAbroadAnswerSaver->save($applicationStep, $irhpApplication, $postData);
    }
}
