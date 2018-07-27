<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\TypeOfLicenceReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use OlcsTest\Bootstrap;

/**
 * Type of licence review service test
 */
class TypeOfLicenceReviewServiceTest extends MockeryTestCase
{
    /** @var  TypeOfLicence review service */
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new TypeOfLicenceReviewService($this->sm);
    }

    public function testGetConfigFromDataGb()
    {
        $gbContinuationDetail = new ContinuationDetail();

        $mockLicenceGb = m::mock(Licence::class)
            ->shouldReceive('getNiFlag')
            ->andReturn('N')
            ->once()
            ->shouldReceive('getOperatorType')
            ->andReturn('Goods')
            ->once()
            ->shouldReceive('getOperatorLocation')
            ->andReturn('Great Britain')
            ->once()
            ->shouldReceive('getLicenceType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDescription')
                    ->andReturn('foo')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $gbContinuationDetail->setLicence($mockLicenceGb);

        $expected =[
            [
                ['value' => 'continuation-review-operator-location'],
                ['value' => 'Great Britain', 'header' => true]
            ],
            [
                ['value' => 'continuation-review-operator-type'],
                ['value' => 'Goods', 'header' => true]
            ],
            [
                ['value' => 'continuation-review-licence-type'],
                ['value' => 'foo', 'header' => true]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($gbContinuationDetail));
    }

    public function testGetConfigFromDataNi()
    {
        $niContinuationDetail = new ContinuationDetail();

        $mockLicenceNi = m::mock(Licence::class)
            ->shouldReceive('getNiFlag')
            ->andReturn('Y')
            ->once()
            ->shouldReceive('getOperatorLocation')
            ->andReturn('Northern Ireland')
            ->once()
            ->shouldReceive('getLicenceType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDescription')
                    ->andReturn('foo')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $niContinuationDetail->setLicence($mockLicenceNi);

        $expected = [
            [
                ['value' => 'continuation-review-operator-location'],
                ['value' => 'Northern Ireland', 'header' => true]
            ],
            [
                ['value' => 'continuation-review-licence-type'],
                ['value' => 'foo', 'header' => true]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($niContinuationDetail));
    }
}
