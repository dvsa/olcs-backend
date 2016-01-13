<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Printer Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="printer")
 */
abstract class AbstractPrinter implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

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
     * Printer name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="printer_name", length=45, nullable=true)
     */
    protected $printerName;

    /**
     * Printer tray
     *
     * @var string
     *
     * @ORM\Column(type="string", name="printer_tray", length=45, nullable=true)
     */
    protected $printerTray;

    /**
     * Team
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\PrintScan\TeamPrinter", mappedBy="printer")
     */
    protected $teams;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->teams = new ArrayCollection();
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return Printer
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Printer
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
     * Set the printer name
     *
     * @param string $printerName
     * @return Printer
     */
    public function setPrinterName($printerName)
    {
        $this->printerName = $printerName;

        return $this;
    }

    /**
     * Get the printer name
     *
     * @return string
     */
    public function getPrinterName()
    {
        return $this->printerName;
    }

    /**
     * Set the printer tray
     *
     * @param string $printerTray
     * @return Printer
     */
    public function setPrinterTray($printerTray)
    {
        $this->printerTray = $printerTray;

        return $this;
    }

    /**
     * Get the printer tray
     *
     * @return string
     */
    public function getPrinterTray()
    {
        return $this->printerTray;
    }

    /**
     * Set the team
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $teams
     * @return Printer
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * Get the teams
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Add a teams
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $teams
     * @return Printer
     */
    public function addTeams($teams)
    {
        if ($teams instanceof ArrayCollection) {
            $this->teams = new ArrayCollection(
                array_merge(
                    $this->teams->toArray(),
                    $teams->toArray()
                )
            );
        } elseif (!$this->teams->contains($teams)) {
            $this->teams->add($teams);
        }

        return $this;
    }

    /**
     * Remove a teams
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $teams
     * @return Printer
     */
    public function removeTeams($teams)
    {
        if ($this->teams->contains($teams)) {
            $this->teams->removeElement($teams);
        }

        return $this;
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
