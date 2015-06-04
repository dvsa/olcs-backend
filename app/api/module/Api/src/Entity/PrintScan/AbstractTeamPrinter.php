<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\JsonSerializableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * TeamPrinter Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="team_printer",
 *    indexes={
 *        @ORM\Index(name="ix_team_printer_printer_id", columns={"printer_id"}),
 *        @ORM\Index(name="ix_team_printer_team_id", columns={"team_id"})
 *    }
 * )
 */
abstract class AbstractTeamPrinter implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * Document type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_type", length=45, nullable=true)
     */
    protected $documentType;

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
     * Printer
     *
     * @var \Dvsa\Olcs\Api\Entity\PrintScan\Printer
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\PrintScan\Printer", fetch="LAZY")
     * @ORM\JoinColumn(name="printer_id", referencedColumnName="id", nullable=false)
     */
    protected $printer;

    /**
     * Team
     *
     * @var \Dvsa\Olcs\Api\Entity\User\Team
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     */
    protected $team;

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
     * Set the id
     *
     * @param int $id
     * @return TeamPrinter
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
     * Set the printer
     *
     * @param \Dvsa\Olcs\Api\Entity\PrintScan\Printer $printer
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
     * @return \Dvsa\Olcs\Api\Entity\PrintScan\Printer
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * Set the team
     *
     * @param \Dvsa\Olcs\Api\Entity\User\Team $team
     * @return TeamPrinter
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Dvsa\Olcs\Api\Entity\User\Team
     */
    public function getTeam()
    {
        return $this->team;
    }



    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
