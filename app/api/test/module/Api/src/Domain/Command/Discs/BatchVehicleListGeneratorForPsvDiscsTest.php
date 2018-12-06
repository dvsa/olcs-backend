<?php

/**
 * BatchVehicleListGeneratorForPsvDiscs Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Discs;

use Dvsa\Olcs\Api\Domain\Command\Discs\BatchVehicleListGeneratorForPsvDiscs;
use PHPUnit_Framework_TestCase;

/**
 * BatchVehicleListGeneratorForPsvDiscs Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class BatchVehicleListGeneratorForPsvDiscsTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = BatchVehicleListGeneratorForPsvDiscs::create(
            [
                'bookmarks' => ['b1', 'b2'],
                'queries' => ['q1', 'q2'],
                'user' => 1
            ]
        );

        $this->assertEquals(['b1', 'b2'], $command->getBookmarks());
        $this->assertEquals(['q1', 'q2'], $command->getQueries());
        $this->assertEquals(1, $command->getUser());
    }
}
