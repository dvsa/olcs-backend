<?php

/**
 * Traffic Area Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\TrafficAreaReviewService;

/**
 * Traffic Area Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new TrafficAreaReviewService($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [
            'licence' => [
                'trafficArea' => [
                    'name' => 'foo'
                ]
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-traffic-area-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-traffic-area',
                        'value' => 'foo'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
