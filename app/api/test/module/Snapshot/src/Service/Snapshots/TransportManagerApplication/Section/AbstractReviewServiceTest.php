<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\AbstractReviewService;
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

/**
 * Stub class for testing AbstractReviewService
 */
class AbstractReviewServiceStub extends AbstractReviewService
{
    public function getConfig(TransportManagerApplication $tma)
    {
    }
}
