<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\ThirdCountryAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\ClientReturnCodeHandler;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ThirdCountryAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ThirdCountryAnswerSaverTest extends MockeryTestCase
{
    private $postData;

    private $applicationStep;

    private $qaContext;

    private $genericAnswerFetcher;

    private $genericAnswerWriter;

    private $clientReturnCodeHandler;

    private $thirdCountryAnswerSaver;

    public function setUp()
    {
        $this->postData = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->qaContext = m::mock(QaContext::class);
        $this->qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($this->applicationStep);

        $this->genericAnswerFetcher = m::mock(GenericAnswerFetcher::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $this->clientReturnCodeHandler = m::mock(ClientReturnCodeHandler::class);

        $this->thirdCountryAnswerSaver = new ThirdCountryAnswerSaver(
            $this->genericAnswerFetcher,
            $this->genericAnswerWriter,
            $this->clientReturnCodeHandler
        );
    }

    public function testSaveCabotageRequired()
    {
        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData)
            ->andReturn('Y');

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, 'qanda.bilaterals.third-country.yes-answer')
            ->once();

        $this->assertNull(
            $this->thirdCountryAnswerSaver->save($this->qaContext, $this->postData)
        );
    }

    public function testSaveNoCabotageRequired()
    {
        $clientReturnCode = 'RETURN_CODE';

        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData)
            ->andReturn('N');

        $this->clientReturnCodeHandler->shouldReceive('handle')
            ->with($this->qaContext)
            ->once()
            ->andReturn($clientReturnCode);

        $this->assertEquals(
            $clientReturnCode,
            $this->thirdCountryAnswerSaver->save($this->qaContext, $this->postData)
        );
    }
}
