<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Template Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="template")
 */
class Template implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * History
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\History", mappedBy="templates")
     */
    protected $historys;

    /**
     * Data
     *
     * @var string
     *
     * @ORM\Column(type="text", name="data", nullable=true)
     */
    protected $data;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->historys = new ArrayCollection();
    }

    /**
     * Set the history
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $historys

     * @return \Olcs\Db\Entity\Template
     */
    public function setHistorys($historys)
    {
        $this->historys = $historys;

        return $this;
    }

    /**
     * Get the history
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getHistorys()
    {
        return $this->historys;
    }

    /**
     * Set the data
     *
     * @param string $data
     * @return \Olcs\Db\Entity\Template
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
