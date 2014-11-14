<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamPrinter Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="team_printer",
 *    indexes={
 *        @ORM\Index(name="fk_team_has_printer_printer1_idx", 
 *            columns={"printer_id"}),
 *        @ORM\Index(name="fk_team_has_printer_team1_idx", 
 *            columns={"team_id"})
 *    }
 * )
 */
class TeamPrinter implements Interfaces\EntityInterface
{

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
     * Team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     */
    protected $team;

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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the team
     *
     * @param \Olcs\Db\Entity\Team $team
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Olcs\Db\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
