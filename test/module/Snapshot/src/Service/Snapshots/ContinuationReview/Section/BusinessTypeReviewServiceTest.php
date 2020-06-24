<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\BusinessTypeReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Business type review service test
 */
class BusinessTypeReviewServiceTest extends MockeryTestCase
{
    /** @var  BusinessType review service */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new BusinessTypeReviewService();
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                ->shouldReceive('getType')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getDescription')
                    ->andReturn('Limited company')
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
            [
                ['value' => 'continuation-review-type-of-business'],
                ['value' => 'Limited company', 'header' => true]
            ],
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }
}
