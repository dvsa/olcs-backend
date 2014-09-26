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
 *        @ORM\Index(name="IDX_723508DA46EC494A", columns={"printer_id"}),
 *        @ORM\Index(name="IDX_723508DA296CD8AE", columns={"team_id"})
 *    }
 * )
 */
class TeamPrinter implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TeamManyToOne;

    /**
     * Printer
     *
     * @var \Olcs\Db\Entity\Printer
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Printer", fetch="LAZY")
     * @ORM\JoinColumn(name="printer_id", referencedColumnName="id", nullable=false)
     */
    protected $printer;

    /**
     * Document type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_type", length=45, nullable=true)
     */
    protected $documentType;

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
}
