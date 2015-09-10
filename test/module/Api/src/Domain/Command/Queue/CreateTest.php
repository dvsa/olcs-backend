<?php

/**
 * Queue Create command test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Queue;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use PHPUnit_Framework_TestCase;

/**
 * Queue Create command test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $params  = [
            'type' => 'foo',
            'status' => 'bar',
            'entityId' => 1
        ];
        $command = Create::create($params);

        $this->assertEquals($params, $command->getArrayCopy());
    }
}
