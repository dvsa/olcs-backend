<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TeamPrinter Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="team_printer",
 *    indexes={
 *        @ORM\Index(name="ix_team_printer_printer_id", columns={"printer_id"}),
 *        @ORM\Index(name="ix_team_printer_team_id", columns={"team_id"})
 *    }
 * )
 */
class TeamPrinter implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TeamManyToOne;

    /**
     * Document type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_type", length=45, nullable=true)
     */
    protected $documentType;

    /**
     * Printer
     *
     * @var \Olcs\Db\Entity\Printer
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Printer")
     * @ORM\JoinColumn(name="printer_id", referencedColumnName="id", nullable=false)
     */
    protected $printer;

    /**
     * Set the document type
     *
     * @param string $documentType
     * @return TeamPrinter
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;

        return $this;
    }

    /**
     * Get the document type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * Set the printer
     *
     * @param \Olcs\Db\Entity\Printer $printer
     * @return TeamPrinter
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;

        return $this;
    }

    /**
     * Get the printer
     *
     * @return \Olcs\Db\Entity\Printer
     */
    public function getPrinter()
    {
        return $this->printer;
    }
}
