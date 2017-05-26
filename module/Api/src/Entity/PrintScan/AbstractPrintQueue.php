<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * PrintQueue Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="print_queue",
 *    indexes={
 *        @ORM\Index(name="ix_print_queue_team_printer_id", columns={"team_printer_id"}),
 *        @ORM\Index(name="ix_print_queue_document_id", columns={"document_id"})
 *    }
 * )
 */
abstract class AbstractPrintQueue implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

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
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document", fetch="LAZY")
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
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter", fetch="LAZY")
     * @ORM\JoinColumn(name="team_printer_id", referencedColumnName="id", nullable=false)
     */
    protected $teamPrinter;

    /**
     * Set the added datetime
     *
     * @param \DateTime $addedDatetime new value being set
     *
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
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getAddedDatetime($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->addedDatetime);
        }

        return $this->addedDatetime;
    }

    /**
     * Set the document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $document entity being set as the value
     *
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
     * @param int $id new value being set
     *
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
     * @param \Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter $teamPrinter entity being set as the value
     *
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



    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}
