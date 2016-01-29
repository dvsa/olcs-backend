<?php

/**
 * BatchVehicleListGeneratorForGoodsDiscs Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\BatchVehicleListGeneratorForGoodsDiscs;
use PHPUnit_Framework_TestCase;

/**
 * BatchVehicleListGeneratorForGoodsDiscs Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class BatchVehicleListGeneratorForGoodsDiscsTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = BatchVehicleListGeneratorForGoodsDiscs::create(
            [
                'licences' => [1, 2],
            ]
        );

        $this->assertEquals([1, 2], $command->getLicences());
    }
}
