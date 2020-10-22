<?php

/**
 * Variation Business Type Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationBusinessTypeReviewService;

/**
 * Variation Business Type Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationBusinessTypeReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationBusinessTypeReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [];

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('variation-review-business-type-change')
            ->andReturn('variation-review-business-type-change-translated');

        $expected = [
            'freetext' => 'variation-review-business-type-change-translated'
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
