<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\LicenceHolderName;

/**
 * Licence holder name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceHolderNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new LicenceHolderName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new LicenceHolderName();
        $bookmark->setData(
            [
                'organisation' => [
                    'name' => 'Org 1'
                ]
            ]
        );

        $this->assertEquals(
            'Org 1',
            $bookmark->render()
        );
    }
}
