<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerClearer;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * GenericAnswerClearerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerClearerTest extends MockeryTestCase
{
    private $applicationStep;

    private $irhpApplication;

    private $genericAnswerProvider;

    private $answerRepo;

    private $genericAnswerClearer;

    public function setUp()
    {
        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->genericAnswerProvider = m::mock(GenericAnswerProvider::class);

        $this->answerRepo = m::mock(AnswerRepository::class);

        $this->genericAnswerClearer = new GenericAnswerClearer(
            $this->genericAnswerProvider,
            $this->answerRepo
        );
    }

    public function testClear()
    {
        $answer = m::mock(Answer::class);

        $this->genericAnswerProvider->shouldReceive('get')
            ->with($this->applicationStep, $this->irhpApplication)
            ->andReturn($answer);

        $this->answerRepo->shouldReceive('delete')
            ->with($answer)
            ->once();

        $this->genericAnswerClearer->clear($this->applicationStep, $this->irhpApplication);
    }

    public function testClearDeleteNotRequired()
    {
        $this->genericAnswerProvider->shouldReceive('get')
            ->with($this->applicationStep, $this->irhpApplication)
            ->andThrow(new NotFoundException());

        $this->answerRepo->shouldReceive('delete')
            ->never();

        $this->genericAnswerClearer->clear($this->applicationStep, $this->irhpApplication);
    }
}