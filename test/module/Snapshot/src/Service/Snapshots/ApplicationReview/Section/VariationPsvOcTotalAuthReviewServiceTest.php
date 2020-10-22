<?php

/**
 * Variation Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationPsvOcTotalAuthReviewService;

/**
 * Variation Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvOcTotalAuthReviewServiceTest extends MockeryTestCase
{

    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationPsvOcTotalAuthReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithoutChanges()
    {
        $data = [
            'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthVehicles' => 10,
            'licence' => [
                'totAuthVehicles' => 10
            ]
        ];

        $this->assertNull($this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChanges()
    {
        $data = [
            'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthVehicles' => 9,
            'licence' => [
                'totAuthVehicles' => 3
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'increased from 3 to 9'
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChangesWithStandardInternational()
    {
        $data = [
            'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'totAuthVehicles' => 9,
            'totCommunityLicences' => 5,
            'licence' => [
                'totAuthVehicles' => 3,
                'totCommunityLicences' => 1,
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'increased from 3 to 9'
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
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
