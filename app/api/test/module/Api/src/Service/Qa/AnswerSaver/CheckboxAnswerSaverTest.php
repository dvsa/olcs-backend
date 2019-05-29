<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\CheckboxAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CheckboxAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckboxAnswerSaverTest extends MockeryTestCase
{
    private $fieldsetName;

    private $applicationStep;

    private $irhpApplication;

    private $genericAnswerWriter;

    private $checkboxAnswerSaver;

    public function setUp()
    {
        $this->fieldsetName = 'fields123';

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getFieldsetName')
            ->andReturn($this->fieldsetName);

        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $this->checkboxAnswerSaver = new CheckboxAnswerSaver($this->genericAnswerWriter);
    }

    public function testSaveChecked()
    {
        $postData = [
            $this->fieldsetName => [
                'qaElement' => '1'
            ]
        ];

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->applicationStep, $this->irhpApplication, true);

        $this->checkboxAnswerSaver->save($this->applicationStep, $this->irhpApplication, $postData);
    }

    public function testSaveUnchecked()
    {
        $postData = [
            $this->fieldsetName => [
            ]
        ];

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->applicationStep, $this->irhpApplication, false);

        $this->checkboxAnswerSaver->save($this->applicationStep, $this->irhpApplication, $postData);
    }
}
