<?php

/**
 * Queue Failed command test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Queue;

use Dvsa\Olcs\Api\Domain\Command\Queue\Failed;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use PHPUnit_Framework_TestCase;

/**
 * Queue Failed command test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FailedTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $item = new QueueEntity();
        $command = Failed::create(['item' => $item]);

        $this->assertSame($item, $command->getItem());
        $this->assertEquals(['item' => $item], $command->getArrayCopy());
    }
}
