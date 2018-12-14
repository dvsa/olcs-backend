<?php

/**
 * BkmOperatorAddress5 Test
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\BkmOperatorAddress5 as Sut;

/**
 * BkmOperatorAddress5 Test
 */
class BkmOperatorAddress5Test extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $bookmark = new Sut();
        $this->assertEquals('', $bookmark->render());
    }
}
