<?php

/**
 * Update Application Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use PHPUnit_Framework_TestCase;

/**
 * Update Application Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateApplicationCompletionTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateApplicationCompletion::create(['id' => 111, 'foo' => 'bar', 'section' => 'foo_section']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals('foo_section', $command->getSection());
        $this->assertEquals(['id' => 111, 'section' => 'foo_section'], $command->getArrayCopy());
    }
}
