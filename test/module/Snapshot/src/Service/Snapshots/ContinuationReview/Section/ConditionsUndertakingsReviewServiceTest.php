<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\ConditionsUndertakingsReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use OlcsTest\Bootstrap;

/**
 * Conditions & undertakings review service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ConditionsUndertakingsReviewServiceTest extends MockeryTestCase
{
    /** @var VehiclesReviewService review service */
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new ConditionsUndertakingsReviewService($this->sm);
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getGroupedConditionsUndertakings')
            ->andReturn(['foo'])
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
            'mainItems' => [
                [
                    'partial' => 'continuation-conditions-undertakings',
                    'variables' => ['conditionsUndertakings' => ['foo']]
                ],
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }
}
