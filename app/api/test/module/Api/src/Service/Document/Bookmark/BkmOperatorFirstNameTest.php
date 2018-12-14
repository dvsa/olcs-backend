<?php

/**
 * BkmOperatorFirstName Test
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\BkmOperatorFirstName as Sut;

/**
 * BkmOperatorFirstName Test
 */
class BkmOperatorFirstNameTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $bookmark = new Sut();
        $this->assertEquals('Sir or Madam', $bookmark->render());
    }
}
