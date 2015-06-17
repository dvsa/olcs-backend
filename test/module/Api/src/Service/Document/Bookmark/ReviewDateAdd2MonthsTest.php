<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\ReviewDateAdd2Months;

/**
 * ReviewDateAdd2Months bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ReviewDateAdd2MonthsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new ReviewDateAdd2Months();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoReviewDate()
    {
        $bookmark = new ReviewDateAdd2Months();
        $bookmark->setData(
            [
                'reviewDate' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithReviewDate()
    {
        $bookmark = new ReviewDateAdd2Months();
        $bookmark->setData(
            [
                'reviewDate' => '2014-02-01'
            ]
        );

        $this->assertEquals(
            '01/04/2014',
            $bookmark->render()
        );
    }
}
