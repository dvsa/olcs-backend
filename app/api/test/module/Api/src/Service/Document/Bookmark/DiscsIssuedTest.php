<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\DiscsIssued;

/**
 * Discs Issued test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscsIssuedTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new DiscsIssued();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new DiscsIssued();
        $bookmark->setData(
            [
                'notCeasedPsvDiscCount' => 76253,
            ]
        );

        $this->assertEquals(76253, $bookmark->render());
    }
}
