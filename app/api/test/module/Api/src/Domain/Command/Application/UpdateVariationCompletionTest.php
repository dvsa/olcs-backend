<?php

/**
 * UpdateVariationCompletion
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateVariationCompletion;
use PHPUnit_Framework_TestCase;

/**
 * UpdateVariationCompletion
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateVariationCompletionTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateVariationCompletion::create(
            [
                'id' => 563,
                'foo' => 'bar',
                'section' => 'foobar',
            ]
        );

        $this->assertEquals(563, $command->getId());
        $this->assertEquals('foobar', $command->getSection());
    }
}
