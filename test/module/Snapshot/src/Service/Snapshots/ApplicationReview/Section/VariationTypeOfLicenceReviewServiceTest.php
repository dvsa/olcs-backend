<?php

/**
 * Variation Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationTypeOfLicenceReviewService;

/**
 * Variation Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationTypeOfLicenceReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [
            'licenceType' => [
                'description' => 'foo'
            ],
            'licence' => [
                'licenceType' => [
                    'description' => 'bar'
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('variation-application-type-of-licence-freetext', 'snapshot')
            ->andReturn('translated-text-%s-%s');

        $this->assertEquals(['freetext' => 'translated-text-bar-foo'], $this->sut->getConfigFromData($data));
    }
}
