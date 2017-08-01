<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Applicationtype as Sut;

/**
 * ApplicationtypeTest
 */
class ApplicationtypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['application' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
        $this->assertSame(123, $query->getId());
        $this->assertSame([], $query->getBundle());
    }

    public function testRender()
    {
        $bookmark = new Sut();
        $bookmark->setData(['licenceType' => ['description' => 'DESCRIPTION']]);

        $this->assertEquals('DESCRIPTION', $bookmark->render());
    }
}
