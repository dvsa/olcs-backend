<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\InitialiseScope;

/**
 * Initialise Scope test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class InitialiseScopeTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = InitialiseScope::create(
            [
                'stockId' => 7
            ]
        );

        static::assertEquals(7, $sut->getStockId());
    }
}
