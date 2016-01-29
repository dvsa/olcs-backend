<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Document\Bookmark\FeeDueDate;

/**
 * Fee Due Date test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeDueDateTest extends MockeryTestCase
{
    public function testGetQuery()
    {
        $bookmark = new FeeDueDate();
        $query = $bookmark->getQuery(['fee' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new FeeDueDate();
        $bookmark->setData(
            [
                'invoicedDate' => '2015-01-01'
            ]
        );

        $dateTime = new \DateTime('2015-01-15');

        $dateHelper = m::mock('\Dvsa\Olcs\Api\Service\Date');

        $dateHelper->shouldReceive('calculateDate')
            ->with(m::type(\DateTime::class), 15)
            ->once()
            ->andReturn($dateTime);

        $bookmark->setDateHelper($dateHelper);

        $this->assertEquals(
            '15/01/2015',
            $bookmark->render()
        );
    }
}
