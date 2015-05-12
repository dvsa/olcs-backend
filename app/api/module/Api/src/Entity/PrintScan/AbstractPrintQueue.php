<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrintQueue Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\Table(name="print_queue",
 *    indexes={
 *        @ORM\Index(name="ix_print_queue_team_printer_id", columns={"team_printer_id"}),
 *        @ORM\Index(name="ix_print_queue_document_id", columns={"document_id"})
 *    }
 * )
 */
abstract class AbstractPrintQueue
{

    /**
     * Added datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="added_datetime", nullable=true)
     */
    protected $addedDatetime;

    /**
     * Document
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\Document
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    protected $document;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Team printer
     *
     * @var \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter")
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
     * Set the document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $document
     * @return PrintQueue
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return PrintQueue
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the team printer
     *
     * @param \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter $teamPrinter
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
     * @return \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter
     */
    public function getTeamPrinter()
    {
        return $this->teamPrinter;
    }


}
