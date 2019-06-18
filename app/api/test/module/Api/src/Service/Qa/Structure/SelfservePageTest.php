<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\FilteredTranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePage;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * SelfservePageTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SelfservePageTest extends MockeryTestCase
{
    private $applicationReference;

    private $applicationStep;

    private $questionText;

    private $nextStepSlug;

    private $selfservePage;

    public function setUp()
    {
        $this->applicationReference = 'OB123456 / 12300';

        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->questionText = m::mock(QuestionText::class);

        $this->nextStepSlug = 'removals-cabotage';

        $this->selfservePage = new SelfservePage(
            $this->applicationReference,
            $this->applicationStep,
            $this->questionText,
            $this->nextStepSlug
        );
    }

    public function testGenerate()
    {
        $applicationStepRepresentation = ['applicationStepRepresentation'];

        $questionTextRepresentation = ['questionTextRepresentation'];

        $this->applicationStep->shouldReceive('getRepresentation')
            ->andReturn($applicationStepRepresentation);

        $this->questionText->shouldReceive('getRepresentation')
            ->andReturn($questionTextRepresentation);

        $expectedRepresentation = [
            'applicationReference' => $this->applicationReference,
            'applicationStep' => $applicationStepRepresentation,
            'questionText' => $questionTextRepresentation,
            'nextStepSlug' => $this->nextStepSlug
        ];

        $this->assertSame(
            $expectedRepresentation,
            $this->selfservePage->getRepresentation()
        );
    }

    public function testGetQuestionText()
    {
        $this->assertSame(
            $this->questionText,
            $this->selfservePage->getQuestionText()
        );
    }
}