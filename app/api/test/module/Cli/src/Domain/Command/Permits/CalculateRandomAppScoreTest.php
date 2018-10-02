<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\CalculateRandomAppScore;

/**
 * Calculate Random Application Score test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class CalculateRandomAppScoreTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = CalculateRandomAppScore::create(
            [
                'stockId' => 7
            ]
        );

        static::assertEquals(7, $sut->getStockId());
    }
}
