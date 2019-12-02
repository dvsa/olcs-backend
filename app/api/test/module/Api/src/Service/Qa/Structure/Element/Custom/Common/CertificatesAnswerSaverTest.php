<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Common;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question as QuestionEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common\CertificatesAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\BaseAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CertificatesAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CertificatesAnswerSaverTest extends MockeryTestCase
{
    public function testSave()
    {
        $postData = [
            'fieldset68' => [
                'qaElement' => '14'
            ]
        ];

        $applicationStep = m::mock(ApplicationStepEntity::class);
        $irhpApplication = m::mock(IrhpApplicationEntity::class);

        $baseAnswerSaver = m::mock(BaseAnswerSaver::class);
        $baseAnswerSaver->shouldReceive('save')
            ->with($applicationStep, $irhpApplication, $postData, QuestionEntity::QUESTION_TYPE_BOOLEAN)
            ->once();

        $certificatesAnswerSaver = new CertificatesAnswerSaver($baseAnswerSaver);
        $certificatesAnswerSaver->save($applicationStep, $irhpApplication, $postData);
    }
}
