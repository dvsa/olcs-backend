<?php

namespace Dvsa\OlcsTest\Api\Entity\PrintScan;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\PrintScan\Printer as Entity;

/**
 * Printer Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PrinterEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testCanDelete()
    {
        $printer = new Entity();
        $this->assertTrue($printer->canDelete());

        $printer->addTeams(new \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter());
        $this->assertFalse($printer->canDelete());
    }
}
