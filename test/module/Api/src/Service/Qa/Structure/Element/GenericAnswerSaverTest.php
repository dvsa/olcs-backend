<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\BaseAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * GenericAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerSaverTest extends MockeryTestCase
{
    public function testSave()
    {
        $postData = [
            'fieldset13' => [
                'qaElement' => 'qaElementValue'
            ]
        ];

        $applicationStep = m::mock(ApplicationStep::class);
        $irhpApplication = m::mock(IrhpApplication::class);

        $baseAnswerSaver = m::mock(BaseAnswerSaver::class);
        $baseAnswerSaver->shouldReceive('save')
            ->with($applicationStep, $irhpApplication, $postData)
            ->once();

        $genericAnswerSaver = new GenericAnswerSaver($baseAnswerSaver);
        $genericAnswerSaver->save($applicationStep, $irhpApplication, $postData);
    }
}
