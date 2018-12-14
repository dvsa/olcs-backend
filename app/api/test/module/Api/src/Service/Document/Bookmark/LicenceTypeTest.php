<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\LicenceType;

/**
 * Licence type test.
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new LicenceType();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new LicenceType();
        $bookmark->setData(
            array(
                'goodsOrPsv' => array(
                    'description' => 'foo'
                ),
                'licenceType' => array(
                    'description' => 'bar'
                )
            )
        );

        $this->assertEquals('foo bar', $bookmark->render());
    }
}
