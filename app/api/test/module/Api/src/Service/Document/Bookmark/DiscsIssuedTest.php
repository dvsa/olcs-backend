<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\DiscsIssued;

/**
 * Discs Issued test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscsIssuedTest extends \PHPUnit_Framework_TestCase
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
                'psvDiscs' => [
                    [
                        'ceasedDate' => null,
                    ], [
                        'ceasedDate' => null,
                    ], [
                        'ceasedDate' => '2015-01-01',
                    ]
                ]
            ]
        );

        $this->assertEquals(2, $bookmark->render());
    }
}
