<?php

namespace Dvsa\OlcsTest\Api\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\User\Team as Entity;
use Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter;
use Dvsa\Olcs\Api\Entity\PrintScan\Printer;

/**
 * Team Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class TeamEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetDefaultTeamPrinter()
    {
        $team = new Entity();
        $teamPrinters = new ArrayCollection();
        $teamPrinter = new TeamPrinter($team, new Printer());
        $teamPrinters->add($teamPrinter);
        $team->setTeamPrinters($teamPrinters);

        $this->assertEquals($teamPrinter, $team->getDefaultTeamPrinter());
    }

    public function testUpdateDefaultPrinterWhenExists()
    {
        $team = new Entity();
        $teamPrinters = new ArrayCollection();
        $teamPrinter = new TeamPrinter($team, new Printer());
        $teamPrinters->add($teamPrinter);
        $team->setTeamPrinters($teamPrinters);

        $newPrinter = new Printer();
        $team->updateDefaultPrinter($newPrinter);
        $this->assertEquals($newPrinter, $team->getDefaultTeamPrinter()->getPrinter());
    }

    public function testUpdateDefaultPrinterWhenNotExists()
    {
        $team = new Entity();
        $team->setTeamPrinters(new ArrayCollection());

        $newPrinter = new Printer();
        $team->updateDefaultPrinter($newPrinter);
        $this->assertEquals($newPrinter, $team->getDefaultTeamPrinter()->getPrinter());
    }
}
