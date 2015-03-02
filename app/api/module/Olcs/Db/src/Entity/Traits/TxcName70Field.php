<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Txc name70 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TxcName70Field
{
    /**
     * Txc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_name", length=70, nullable=true)
     */
    protected $txcName;

    /**
     * Set the txc name
     *
     * @param string $txcName
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTxcName($txcName)
    {
        $this->txcName = $txcName;

        return $this;
    }

    /**
     * Get the txc name
     *
     * @return string
     */
    public function getTxcName()
    {
        return $this->txcName;
    }
}
