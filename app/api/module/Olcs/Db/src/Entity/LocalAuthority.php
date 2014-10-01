<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LocalAuthority Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="local_authority",
 *    indexes={
 *        @ORM\Index(name="IDX_B4BEE5A018E0B1DB", columns={"traffic_area_id"}),
 *        @ORM\Index(name="IDX_B4BEE5A065CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_B4BEE5A0DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class LocalAuthority implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\Description255Field,
        Traits\EmailAddress45Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Txc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_name", length=255, nullable=true)
     */
    protected $txcName;

    /**
     * Naptan code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="naptan_code", length=3, nullable=true)
     */
    protected $naptanCode;

    /**
     * Set the txc name
     *
     * @param string $txcName
     * @return LocalAuthority
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

    /**
     * Set the naptan code
     *
     * @param string $naptanCode
     * @return LocalAuthority
     */
    public function setNaptanCode($naptanCode)
    {
        $this->naptanCode = $naptanCode;

        return $this;
    }

    /**
     * Get the naptan code
     *
     * @return string
     */
    public function getNaptanCode()
    {
        return $this->naptanCode;
    }
}
