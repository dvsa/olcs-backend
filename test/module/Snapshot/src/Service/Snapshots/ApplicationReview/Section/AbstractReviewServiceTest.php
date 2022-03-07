<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section\Stub\AbstractReviewServiceStub;

class AbstractReviewServiceTest extends MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $this->sut = new AbstractReviewServiceStub();
    }

    public function testGetHeaderTranslationKey()
    {
        $this->assertEquals(
            'review-section-name',
            $this->sut->getHeaderTranslationKey([], 'section-name')
        );
    }
}
