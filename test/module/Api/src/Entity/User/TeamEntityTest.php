<?php

declare(strict_types = 1);

namespace Dvsa\OlcsTest\Api\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\User\Team as Entity;
use Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter;
use Dvsa\Olcs\Api\Entity\PrintScan\Printer;
use Mockery as m;

/**
 * Team Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 *
 * @see Entity
 */
class TeamEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetDefaultTeamPrinter(): void
    {
        $team = new Entity();
        $teamPrinters = new ArrayCollection();
        $teamPrinter = new TeamPrinter($team, new Printer());
        $teamPrinters->add($teamPrinter);
        $team->setTeamPrinters($teamPrinters);

        $this->assertEquals($teamPrinter, $team->getDefaultTeamPrinter());
    }

    public function testUpdateDefaultPrinterWhenExists(): void
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

    public function testUpdateDefaultPrinterWhenNotExists(): void
    {
        $team = new Entity();
        $team->setTeamPrinters(new ArrayCollection());

        $newPrinter = new Printer();
        $team->updateDefaultPrinter($newPrinter);
        $this->assertEquals($newPrinter, $team->getDefaultTeamPrinter()->getPrinter());
    }

    /**
     * User has access to all data (they are in an excluded team)
     * Also tests that the traffic area check isn't done
     */
    public function testAllDataAccess(): void
    {
        $teamId = 999;
        $excludedTeams = [$teamId];

        $trafficArea = m::mock(TrafficArea::class);
        $trafficArea->expects('getIsNi')->never();

        $expectedAllowedAreas = array_merge(TrafficArea::GB_TA_IDS, TrafficArea::NI_TA_IDS);

        $entity = new Entity();
        $entity->setId($teamId);
        $entity->setTrafficArea($trafficArea);

        $this->assertEquals($expectedAllowedAreas, $entity->getAllowedTrafficAreas($excludedTeams));
    }

    /**
     * @dataProvider dpTestDataAccess
     */
    public function testDataAccess(bool $isNi, array $expectedAllowedAreas): void
    {
        $trafficArea = m::mock(TrafficArea::class);
        $trafficArea->expects('getIsNi')->times(3)->withNoArgs()->andReturn($isNi);

        $entity = new Entity();
        $entity->setId(999);
        $entity->setTrafficArea($trafficArea);

        $this->assertEquals($expectedAllowedAreas, $entity->getAllowedTrafficAreas());
    }

    public function dpTestDataAccess(): array
    {
        return [
            'GB User' => [false, TrafficArea::GB_TA_IDS],
            'NI User' => [true, TrafficArea::NI_TA_IDS],
        ];
    }
}
