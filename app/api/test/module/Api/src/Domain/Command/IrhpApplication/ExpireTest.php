<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\Expire;

/**
 * Set IrhpApplication status to expired test
 */
class ExpireTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $data = ['id' => 1];
        $command = Expire::create($data);
        static::assertEquals(1, $command->getId());
    }
}
