<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\ApplicationCountryRemover;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\CabotageOnlyAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CabotageOnlyAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CabotageOnlyAnswerSaverTest extends MockeryTestCase
{
    private $postData;

    private $applicationStep;

    private $qaContext;

    private $genericAnswerFetcher;

    private $genericAnswerWriter;

    private $applicationCountryRemover;

    private $cabotageOnlyAnswerSaver;

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

        $this->applicationCountryRemover = m::mock(ApplicationCountryRemover::class);

        $this->cabotageOnlyAnswerSaver = new CabotageOnlyAnswerSaver(
            $this->genericAnswerFetcher,
            $this->genericAnswerWriter,
            $this->applicationCountryRemover
        );
    }

    public function testSaveCabotageRequired()
    {
        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData)
            ->andReturn('Y');

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $this->qaContext->shouldReceive('getQaEntity')
            ->andReturn($irhpPermitApplication);

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, Answer::BILATERAL_CABOTAGE_ONLY, Question::QUESTION_TYPE_STRING)
            ->once()
            ->globally()
            ->ordered();

        $this->cabotageOnlyAnswerSaver->save($this->qaContext, $this->postData);
    }

    public function testSaveNoCabotageRequiredDestinationCancel()
    {
        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData)
            ->andReturn('N');

        $country1 = m::mock(Country::class);

        $countries = new ArrayCollection([$country1]);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpApplication->getCountrys')
            ->withNoArgs()
            ->andReturn($countries);

        $this->qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);

        $this->assertEquals(
            CabotageOnlyAnswerSaver::FRONTEND_DESTINATION_CANCEL,
            $this->cabotageOnlyAnswerSaver->save($this->qaContext, $this->postData)
        );
    }

    public function testSaveNoCabotageRequiredDestinationOverview()
    {
        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($this->applicationStep, $this->postData)
            ->andReturn('N');

        $country1 = m::mock(Country::class);
        $country2 = m::mock(Country::class);
        $country3 = m::mock(Country::class);

        $countries = new ArrayCollection([$country1, $country2, $country3]);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpApplication->getCountrys')
            ->withNoArgs()
            ->andReturn($countries);

        $this->qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);

        $this->applicationCountryRemover->shouldReceive('remove')
            ->with($irhpPermitApplication)
            ->once();

        $this->assertEquals(
            CabotageOnlyAnswerSaver::FRONTEND_DESTINATION_OVERVIEW,
            $this->cabotageOnlyAnswerSaver->save($this->qaContext, $this->postData)
        );
    }
}
