<?php

/**
 * Variation Goods Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationGoodsOcTotalAuthReviewService;

/**
 * Variation Goods Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsOcTotalAuthReviewServiceTest extends MockeryTestCase
{

    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationGoodsOcTotalAuthReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithoutChanges()
    {
        $data = [
            'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'licence' => [
                'totAuthVehicles' => 10,
                'totAuthTrailers' => 10
            ]
        ];

        $this->assertNull($this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChanges()
    {
        $data = [
            'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'licence' => [
                'totAuthVehicles' => 20,
                'totAuthTrailers' => 5
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'decreased from 20 to 10'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => 'increased from 5 to 10'
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('review-value-decreased', 'snapshot')
            ->andReturn('decreased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased', 'snapshot')
            ->andReturn('increased from %s to %s');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChangesWithCommunityLicences()
    {
        $data = [
            'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'totCommunityLicences' => 5,
            'licence' => [
                'totAuthVehicles' => 20,
                'totAuthTrailers' => 5,
                'totCommunityLicences' => 1,
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'decreased from 20 to 10'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => 'increased from 5 to 10'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-community-licences',
                        'value' => 'increased from 1 to 5'
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('review-value-decreased', 'snapshot')
            ->andReturn('decreased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased', 'snapshot')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased', 'snapshot')
            ->andReturn('increased from %s to %s');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
