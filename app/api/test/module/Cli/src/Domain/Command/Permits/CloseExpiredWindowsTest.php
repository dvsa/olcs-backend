<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\CloseExpiredWindows;

/**
 * Close expired windows test
 *
 */
class CloseExpiredWindowsTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = CloseExpiredWindows::create(
            [
                'since' => 'TEST'
            ]
        );

        static::assertEquals('TEST', $sut->getSince());
    }
}
