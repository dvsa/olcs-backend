<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;
use Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section\Stub\AbstractReviewServiceStub;
use OlcsTest\Bootstrap;

class AbstractReviewServiceTest extends MockeryTestCase
{
    private $sut;

    /** @var  ServiceManager|\Mockery\MockInterface */
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new AbstractReviewServiceStub();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testTranslate()
    {
        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('foo', 'snapshot')
            ->andReturn('foo_translated')
            ->once()
            ->getMock();
        $this->sm->setService('translator', $mockTranslator);

        $this->assertEquals('foo_translated', $this->sut->translate('foo'));
    }

    /**
     * @dataProvider testFormatDateDataProvider
     */
    public function testFormatDate($expected, $date)
    {
        $this->assertEquals($expected, $this->sut->formatDate($date));
    }

    public function testFormatDateDataProvider()
    {
        return [
            ['15 Aug 2005', '2005-08-15T15:52:01+00:00'],
            ['01 Aug 2017', '2017-08-01T15:52:00+05:00'],
        ];
    }
}
