<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\SerialNoPrefix;

/**
 * Serial No Prefix test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SerialNoPrefixTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new SerialNoPrefix();
        $query = $bookmark->getQuery(['communityLic' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNonZeroIssueNumber()
    {
        $bookmark = new SerialNoPrefix();
        $bookmark->setData(
            [
                'serialNoPrefix' => 'foo'
            ]
        );

        $this->assertEquals(
            'foo',
            $bookmark->render()
        );
    }
}
