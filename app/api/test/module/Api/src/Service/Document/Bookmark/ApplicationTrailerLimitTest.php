<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\ApplicationTrailerLimit;

/**
 * Application Trailer Limit test
 *
 * @author Alex Peskov <alex.peshkov@valtech.co.uk>
 */
class ApplicationTrailerLimitTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new ApplicationTrailerLimit();
        $query = $bookmark->getQuery(['case' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new ApplicationTrailerLimit();
        $bookmark->setData(
            [
                'application' => [
                    'totAuthTrailers' => 1
                ]
            ]
        );

        $this->assertEquals(1, $bookmark->render());
    }
}
