<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\ProceedToStatus;

/**
 * Proceed to status test
 *
 */
class ProceedToStatusTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $sut = ProceedToStatus::create(
            [
                'ids' => [1, 2, 3],
                'status' => 'TEST',
            ]
        );

        static::assertEquals([1, 2, 3], $sut->getIds());
        static::assertEquals('TEST', $sut->getStatus());
        static::assertEquals(
            [
                'ids' => [1, 2, 3],
                'status' => 'TEST',
            ],
            $sut->getArrayCopy()
        );
    }
}
