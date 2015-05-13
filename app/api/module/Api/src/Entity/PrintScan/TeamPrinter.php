<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamPrinter Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="team_printer",
 *    indexes={
 *        @ORM\Index(name="ix_team_printer_printer_id", columns={"printer_id"}),
 *        @ORM\Index(name="ix_team_printer_team_id", columns={"team_id"})
 *    }
 * )
 */
class TeamPrinter extends AbstractTeamPrinter
{

}
