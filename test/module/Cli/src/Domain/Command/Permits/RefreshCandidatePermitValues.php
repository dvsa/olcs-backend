<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\RefreshCandidatePermitValues;

/**
 * Refresh Candidate Permit Values test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class InitialiseScopeTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = RefreshCandidatePermitValues::create(
            [
                'stockId' => 7
            ]
        );

        static::assertEquals(7, $sut->getStockId());
    }
}
