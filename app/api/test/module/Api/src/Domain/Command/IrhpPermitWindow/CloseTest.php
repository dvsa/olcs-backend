<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\Command\IrhpPermitWindow\Close;
use PHPUnit_Framework_TestCase;

/**
 * Close IRHP Permit Window Test
 */
class CloseTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = Close::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
