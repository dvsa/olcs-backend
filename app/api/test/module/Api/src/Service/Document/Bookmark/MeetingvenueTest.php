<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\HearingBundle;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Meetingvenue as Sut;

/**
 * MeetingvenueTest
 */
class MeetingvenueTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['case' => 123]);

        $this->assertInstanceOf(HearingBundle::class, $query);
        $this->assertSame(123, $query->getCase());
        $this->assertSame(['venue'], $query->getBundle());
    }

    public function testRender()
    {
        $bookmark = new Sut();
        $bookmark->setData(['venue' => ['name' => 'VENUE']]);

        $this->assertEquals('VENUE', $bookmark->render());
    }

    public function testRenderVenueOther()
    {
        $bookmark = new Sut();
        $bookmark->setData(['venueOther' => 'OTHER_VENUE']);

        $this->assertEquals('OTHER_VENUE', $bookmark->render());
    }
}
