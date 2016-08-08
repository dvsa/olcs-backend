<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section\Stub\AbstractReviewServiceStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\AbstractReviewService
 */
class AbstractReviewServiceTest extends MockeryTestCase
{
    /** @var  AbstractReviewServiceStub */
    private $sut;

    public function setUp()
    {
        static::markTestSkipped('left for future');

        $this->sut = new AbstractReviewServiceStub();
    }

    public function test()
    {
    }
}
