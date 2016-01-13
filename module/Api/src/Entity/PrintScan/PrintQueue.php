<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrintQueue Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="print_queue",
 *    indexes={
 *        @ORM\Index(name="ix_print_queue_team_printer_id", columns={"team_printer_id"}),
 *        @ORM\Index(name="ix_print_queue_document_id", columns={"document_id"})
 *    }
 * )
 */
class PrintQueue extends AbstractPrintQueue
{

}
