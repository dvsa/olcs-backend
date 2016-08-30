<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\ObjDeadline;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Document\Bookmark\ObjDeadline
 */
class ObjDeadlineTest extends MockeryTestCase
{
    public function test()
    {
        $sut = new ObjDeadline;
        $sut->setData(['pubDate' => '2003-02-01']);

        static::assertEquals('22/02/2003', $sut->render());
    }
}
