<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LegacyPiReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="legacy_pi_reason",
 *    indexes={
 *        @ORM\Index(name="ix_legacy_pi_reason_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_legacy_pi_reason_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class LegacyPiReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255Field,
        Traits\IdIdentity,
        Traits\IsNiField,
        Traits\IsReadOnlyField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\SectionCode50Field,
        Traits\CustomVersionField;

    /**
     * Goods or psv
     *
     * @var string
     *
     * @ORM\Column(type="string", name="goods_or_psv", length=3, nullable=false)
     */
    protected $goodsOrPsv;

    /**
     * Is decision
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_decision", nullable=false)
     */
    protected $isDecision;

    /**
     * Set the goods or psv
     *
     * @param string $goodsOrPsv
     * @return LegacyPiReason
     */
    public function setGoodsOrPsv($goodsOrPsv)
    {
        $this->goodsOrPsv = $goodsOrPsv;

        return $this;
    }

    /**
     * Get the goods or psv
     *
     * @return string
     */
    public function getGoodsOrPsv()
    {
        return $this->goodsOrPsv;
    }

    /**
     * Set the is decision
     *
     * @param boolean $isDecision
     * @return LegacyPiReason
     */
    public function setIsDecision($isDecision)
    {
        $this->isDecision = $isDecision;

        return $this;
    }

    /**
     * Get the is decision
     *
     * @return boolean
     */
    public function getIsDecision()
    {
        return $this->isDecision;
    }
}
