<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrintQueue Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="print_queue",
 *    indexes={
 *        @ORM\Index(name="fk_print_queue_team_printer1_idx", 
 *            columns={"team_printer_id"}),
 *        @ORM\Index(name="fk_print_queue_document1_idx", 
 *            columns={"document_id"})
 *    }
 * )
 */
class PrintQueue implements Interfaces\EntityInterface
{

    /**
     * Team printer
     *
     * @var \Olcs\Db\Entity\TeamPrinter
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TeamPrinter", fetch="LAZY")
     * @ORM\JoinColumn(name="team_printer_id", referencedColumnName="id", nullable=false)
     */
    protected $teamPrinter;

    /**
     * Added datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="added_datetime", nullable=true)
     */
    protected $addedDatetime;

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
     * Document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    protected $document;

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
     * Set the document
     *
     * @param \Olcs\Db\Entity\Document $document
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}
