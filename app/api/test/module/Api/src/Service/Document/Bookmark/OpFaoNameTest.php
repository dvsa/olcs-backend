<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\OpFaoName;

/**
 * Operator FAO name test test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OpFaoNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new OpFaoName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoCorrespondenceAddress()
    {
        $bookmark = new OpFaoName();
        $bookmark->setData(
            [
                'correspondenceCd' => null
            ]
        );

        $this->assertEquals(
            null,
            $bookmark->render()
        );
    }

    public function testRenderWithCorrespondenceAddress()
    {
        $bookmark = new OpFaoName();
        $bookmark->setData(
            [
                'correspondenceCd' => [
                    'fao' => 'Team Leader'
                ]
            ]
        );

        $this->assertEquals(
            'Team Leader',
            $bookmark->render()
        );
    }
}
