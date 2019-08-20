<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\AnswersSummary;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummary as AnswersSummaryObj;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\AnswersSummary as AnswersSummaryQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Zend\I18n\Translator\Translator;

class AnswersSummaryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AnswersSummary();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        $this->mockedSmServices = [
            'PermitsAnswersSummaryGenerator' => m::mock(AnswersSummaryGenerator::class),
            'translator' => m::mock(Translator::class)
        ];

        parent::setUp();
    }

    /**
     * @dataProvider dpHandleQuery
     */
    public function testHandleQuery($translateToWelsh, $expectedLocale)
    {
        $previousLocale = 'sq_AL';

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $answersSummaryRepresentation = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];

        $answersSummary = m::mock(AnswersSummaryObj::class);
        $answersSummary->shouldReceive('getRepresentation')
            ->withNoArgs()
            ->andReturn($answersSummaryRepresentation);

        $this->mockedSmServices['PermitsAnswersSummaryGenerator']->shouldReceive('generate')
            ->with($irhpApplicationEntity)
            ->once()
            ->andReturn($answersSummary);

        $query = AnswersSummaryQry::create(
            [
                'id' => 56,
                'translateToWelsh' => $translateToWelsh
            ]
        );

        $this->repoMap['IrhpApplication']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($irhpApplicationEntity);

        $this->mockedSmServices['translator']->shouldReceive('getLocale')
            ->withNoArgs()
            ->once()
            ->globally()
            ->ordered()
            ->andReturn($previousLocale);

        $this->mockedSmServices['translator']->shouldReceive('setLocale')
            ->with($expectedLocale)
            ->once()
            ->globally()
            ->ordered();

        $this->mockedSmServices['translator']->shouldReceive('setLocale')
            ->with($previousLocale)
            ->once()
            ->globally()
            ->ordered();

        $this->assertEquals(
            $answersSummaryRepresentation,
            $this->sut->handleQuery($query)
        );
    }

    public function dpHandleQuery()
    {
        return [
            ['Y', 'cy_GB'],
            ['N', 'en_GB']
        ];
    }
}
