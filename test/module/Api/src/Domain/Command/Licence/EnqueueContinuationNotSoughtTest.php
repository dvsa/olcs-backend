<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\EnqueueContinuationNotSought;
use PHPUnit_Framework_TestCase;

/**
 * Enqueue CNS Test
 *
 * @author Alex Peshkov <alex.peshkov@vltech.co.uk>
 */
class EnqueueContinuationNotSoughtTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = EnqueueContinuationNotSought::create(['licences' => 'foo', 'date' => 'bar']);

        $this->assertEquals('foo', $command->getLicences());
        $this->assertEquals('bar', $command->getDate());
    }
}
