<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\EndIrhpApplicationsAndPermits;
use PHPUnit\Framework\TestCase;

/**
 * EndIrhpApplicationsAndPermits test
 */
class EndIrhpApplicationsAndPermitsTest extends TestCase
{
    public function testStructure()
    {
        $sut = EndIrhpApplicationsAndPermits::create(
            [
                'id' => 100,
            ]
        );

        static::assertEquals(100, $sut->getId());
        static::assertEquals(
            [
                'id' => 100,
            ],
            $sut->getArrayCopy()
        );
    }
}
