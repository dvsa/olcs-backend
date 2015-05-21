<?php

/**
 * Generate Licence Number Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\GenerateLicenceNumber;
use PHPUnit_Framework_TestCase;

/**
 * Generate Licence Number Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenerateLicenceNumberTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = GenerateLicenceNumber::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
