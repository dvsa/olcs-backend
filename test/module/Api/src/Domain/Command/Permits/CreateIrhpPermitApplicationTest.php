<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;

/**
 * CreateIrhpPermitApplication Test
 *
 */
class CreateIrhpPermitApplicationTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $sut = CreateIrhpPermitApplication::create(
            [
                'window' => 1,
                'ecmtPermitApplication' => 100,
            ]
        );

        static::assertEquals(1, $sut->getWindow());
        static::assertEquals(100, $sut->getEcmtPermitApplication());
        static::assertEquals(
            [
                'window' => 1,
                'ecmtPermitApplication' => 100,
            ],
            $sut->getArrayCopy()
        );
    }
}
