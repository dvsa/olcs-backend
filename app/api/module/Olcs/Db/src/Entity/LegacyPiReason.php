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
 *        @ORM\Index(name="fk_pi_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class LegacyPiReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\SectionCode50Field,
        Traits\Description255Field,
        Traits\IsReadOnlyField,
        Traits\IsNiField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
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
     * @var int
     *
     * @ORM\Column(type="integer", name="is_decision", nullable=false)
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
     * @param int $isDecision
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
     * @return int
     */
    public function getIsDecision()
    {
        return $this->isDecision;
    }
}
