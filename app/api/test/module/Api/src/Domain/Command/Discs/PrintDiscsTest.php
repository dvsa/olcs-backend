<?php

/**
 * Print Discs Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Discs;

use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs;
use PHPUnit_Framework_TestCase;

/**
 * Print Discs Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrintDiscsTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = PrintDiscs::create(
            [
                'discs' => ['d1', 'd2'],
                'type' => 'gv',
                'startNumber' => 1
            ]
        );

        $this->assertEquals(['d1', 'd2'], $command->getDiscs());
        $this->assertEquals('gv', $command->getType());
        $this->assertEquals(1, $command->getStartNumber());
    }
}
