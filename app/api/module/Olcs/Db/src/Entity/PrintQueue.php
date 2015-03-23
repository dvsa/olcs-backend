<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PrintQueue Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="print_queue",
 *    indexes={
 *        @ORM\Index(name="ix_print_queue_team_printer_id", columns={"team_printer_id"}),
 *        @ORM\Index(name="ix_print_queue_document_id", columns={"document_id"})
 *    }
 * )
 */
class PrintQueue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\DocumentManyToOne,
        Traits\IdIdentity;

    /**
     * Added datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="added_datetime", nullable=true)
     */
    protected $addedDatetime;

    /**
     * Team printer
     *
     * @var \Olcs\Db\Entity\TeamPrinter
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TeamPrinter")
     * @ORM\JoinColumn(name="team_printer_id", referencedColumnName="id", nullable=false)
     */
    protected $teamPrinter;

    /**
     * Set the added datetime
     *
     * @param \DateTime $addedDatetime
     * @return PrintQueue
     */
    public function setAddedDatetime($addedDatetime)
    {
        $this->addedDatetime = $addedDatetime;

        return $this;
    }

    /**
     * Get the added datetime
     *
     * @return \DateTime
     */
    public function getAddedDatetime()
    {
        return $this->addedDatetime;
    }

    /**
     * Set the team printer
     *
     * @param \Olcs\Db\Entity\TeamPrinter $teamPrinter
     * @return PrintQueue
     */
    public function setTeamPrinter($teamPrinter)
    {
        $this->teamPrinter = $teamPrinter;

        return $this;
    }

    /**
     * Get the team printer
     *
     * @return \Olcs\Db\Entity\TeamPrinter
     */
    public function getTeamPrinter()
    {
        return $this->teamPrinter;
    }
}
