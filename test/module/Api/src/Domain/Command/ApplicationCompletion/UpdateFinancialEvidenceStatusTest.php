<?php

/**
 * Update Financial Evidence Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateFinancialEvidenceStatus;
use PHPUnit_Framework_TestCase;

/**
 * Update Financial Evidence Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateFinancialEvidenceStatusTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateFinancialEvidenceStatus::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
