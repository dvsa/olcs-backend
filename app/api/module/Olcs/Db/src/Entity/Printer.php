<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Printer Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="printer")
 */
class Printer implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Description255FieldAlt1;

    /**
     * Printer tray
     *
     * @var string
     *
     * @ORM\Column(type="string", name="printer_tray", length=45, nullable=true)
     */
    protected $printerTray;

    /**
     * Printer name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="printer_name", length=45, nullable=true)
     */
    protected $printerName;


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
}
