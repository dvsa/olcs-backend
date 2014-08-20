<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Goods or psv3 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait GoodsOrPsv3Field
{
    /**
     * Goods or psv
     *
     * @var string
     *
     * @ORM\Column(type="string", name="goods_or_psv", length=3, nullable=false)
     */
    protected $goodsOrPsv;

    /**
     * Set the goods or psv
     *
     * @param string $goodsOrPsv
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
}
