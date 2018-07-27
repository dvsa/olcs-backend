<?php

/**
 * Licence Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\LicenceConditionsUndertakingsReviewService;

/**
 * Licence Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakingsReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new LicenceConditionsUndertakingsReviewService($this->sm);
    }

    public function testGetConfigFromData()
    {
        // Params
        $data = [
            [
                'list' => [
                    'foo' => 'bar1'
                ]
            ],
            [
                'list' => [
                    'foo' => 'bar2'
                ]
            ],
            [
                'list' => [
                    'foo' => 'bar3'
                ]
            ],
            [
                'list' => [
                    'foo' => 'bar4'
                ]
            ]
        ];
        $inputData = ['foo' => 'bar']; // Doesn't matter what this is
        $expected = [
            'subSections' => [
                'BAR1',
                'BAR2',
                'BAR3',
                'BAR4'
            ]
        ];

        // Mocks
        $mockConditionsUndertakings = m::mock();
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $this->sm->setService('Review\ConditionsUndertakings', $mockConditionsUndertakings);

        // Expectations
        $mockConditionsUndertakings->shouldReceive('splitUpConditionsAndUndertakings')
            ->with($inputData, false)
            ->andReturn($data)
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo' => 'bar1'], 'application', 'conditions', 'added')
            ->andReturn('BAR1')
            ->shouldReceive('formatLicenceSubSection')
            ->with(['foo' => 'bar2'], 'application', 'undertakings', 'added')
            ->andReturn('BAR2')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo' => 'bar3'], 'application', 'conditions', 'added')
            ->andReturn('BAR3')
            ->shouldReceive('formatOcSubSection')
            ->with(['foo' => 'bar4'], 'application', 'undertakings', 'added')
            ->andReturn('BAR4');

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($inputData));
    }
}
