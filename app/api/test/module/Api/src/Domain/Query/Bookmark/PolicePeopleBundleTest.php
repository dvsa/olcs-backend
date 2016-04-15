<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Bookmark;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PolicePeopleBundle;

/**
 * Class PolicePeopleBundleTest
 * @package Dvsa\OlcsTest\Api\Domain\Query\Bookmark
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PolicePeopleBundleTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $id = 1;
        $bundle = ['bundle'];

        $query = PolicePeopleBundle::create(
            [
                'id' => $id,
                'bundle' => $bundle
            ]
        );

        $this->assertSame($id, $query->getId());
        $this->assertSame($bundle, $query->getBundle());
    }
}
