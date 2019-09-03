<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpPermitApplicationPermit;

/**
 * AllocateIrhpPermitApplicationPermit Test
 *
 */
class AllocateIrhpPermitApplicationPermitTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $expiryDate = new DateTime('3000-01-01');
        $sut = AllocateIrhpPermitApplicationPermit::create(
            [
                'id' => 1,
                'emissionsCategory' => 'emissions_cat_euro6',
                'expiryDate' => $expiryDate
            ]
        );

        static::assertEquals(1, $sut->getId());
        static::assertEquals('emissions_cat_euro6', $sut->getEmissionsCategory());
        static::assertEquals($expiryDate, $sut->getExpiryDate());
        static::assertEquals(
            [
                'id' => 1,
                'emissionsCategory' => 'emissions_cat_euro6',
                'expiryDate' => $expiryDate
            ],
            $sut->getArrayCopy()
        );
    }
}
