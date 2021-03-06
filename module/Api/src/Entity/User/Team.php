<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter;

/**
 * Team Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="team",
 *    indexes={
 *        @ORM\Index(name="ix_team_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_team_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_team_created_by", columns={"created_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_team_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Team extends AbstractTeam
{
    const ERROR_TEAM_EXISTS = 'err_team_exists';
    const ERROR_TEAM_LINKED_TO_USERS = 'err_team_linked_to_users';
    const ERROR_TEAM_LINKED_TO_PRINTER_SETTINGS = 'err_team_linked_to_printer_settings';
    const ERROR_TEAM_LINKED_TO_TASK_ALLOCATION_RULES = 'err_team_linked_to_task_allocation_rules';

    public function getDefaultTeamPrinter()
    {
        $printers = $this->getTeamPrinters();
        $defaultTeamPrinter = $printers->filter(
            function ($pr) {
                return !$pr->getUser() && !$pr->getSubCategory();
            }
        )->first(); // should be only 1 default printer
        return $defaultTeamPrinter ? $defaultTeamPrinter : null;
    }

    public function updateDefaultPrinter($newDefaultPrinter)
    {
        $currentDefaultTeamPrinter = $this->getDefaultTeamPrinter();
        if ($currentDefaultTeamPrinter) {
            $currentDefaultTeamPrinter->setPrinter($newDefaultPrinter);
        } else {
            $teamPrinter = new TeamPrinter($this, $newDefaultPrinter);
            $this->addTeamPrinters($teamPrinter);
        }
    }
}
