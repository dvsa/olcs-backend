<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * GenericAnswerFetcherTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerFetcherTest extends MockeryTestCase
{
    private $fieldsetName;

    private $applicationStepEntity;

    private $genericAnswerFetcher;

    public function setUp()
    {
        $this->fieldsetName = 'fields123';

        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $this->applicationStepEntity->shouldReceive('getFieldsetName')
            ->andReturn($this->fieldsetName);

        $this->genericAnswerFetcher = new GenericAnswerFetcher();
    }

    public function testFetch()
    {
        $answerValue = '123';

        $postData = [
            $this->fieldsetName => [
                'qaElement' => $answerValue
            ]
        ];

        $this->assertEquals(
            $answerValue,
            $this->genericAnswerFetcher->fetch($this->applicationStepEntity, $postData)
        );
    }

    public function testFetchExceptionWhenNoData()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(GenericAnswerFetcher::ERR_NO_ANSWER);

        $postData = [
            'fields456' => [
                'qaElement' => '123'
            ]
        ];

        $this->genericAnswerFetcher->fetch($this->applicationStepEntity, $postData);
    }
}
