<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\BusinessDetailsReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use OlcsTest\Bootstrap;

/**
 * Business details review service test
 */
class BusinessDetailsReviewServiceTest extends MockeryTestCase
{
    /** @var BusinessDetailsReviewService review service */
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new BusinessDetailsReviewService($this->sm);
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $tradingNames = new ArrayCollection();

        $tradingName1 = m::mock()
            ->shouldReceive('getName')
            ->andReturn('bar1')
            ->once()
            ->getMock();

        $tradingName2 = m::mock()
            ->shouldReceive('getName')
            ->andReturn('bar2')
            ->once()
            ->getMock();

        $tradingNames->add($tradingName1);
        $tradingNames->add($tradingName2);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getType')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getId')
                        ->andReturn(Organisation::ORG_TYPE_REGISTERED_COMPANY)
                        ->once()
                        ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getCompanyOrLlpNo')
                    ->andReturn('12345678')
                    ->once()
                    ->shouldReceive('getName')
                    ->andReturn('Foo Ltd')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getTradingNames')
            ->andReturn($tradingNames)
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
            [
                ['value' => 'continuation-review-business-details-company_number'],
                ['value' => '12345678', 'header' => true]
            ],
            [
                ['value' => 'continuation-review-business-details-company_name'],
                ['value' => 'Foo Ltd', 'header' => true]
            ],
            [
                ['value' => 'continuation-review-business-details-trading_names'],
                ['value' => 'bar1, bar2', 'header' => true]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }
}
