<?php

/**
 * Queue Complete command test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Queue;

use Dvsa\Olcs\Api\Domain\Command\Queue\Complete;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use PHPUnit_Framework_TestCase;

/**
 * Queue Complete command test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompleteTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $item = new QueueEntity();
        $command = Complete::create(['item' => $item]);

        $this->assertSame($item, $command->getItem());
        $this->assertEquals(['item' => $item], $command->getArrayCopy());
    }
}
