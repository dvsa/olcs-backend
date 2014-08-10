<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Goods or psv many to one trait
 *
 * Auto-Generated (Shared between 4 entities)
 */
trait GoodsOrPsvManyToOne
{
    /**
     * Goods or psv
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="goods_or_psv", referencedColumnName="id")
     */
    protected $goodsOrPsv;

    /**
     * Set the goods or psv
     *
     * @param \Olcs\Db\Entity\RefData $goodsOrPsv
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getGoodsOrPsv()
    {
        return $this->goodsOrPsv;
    }

}
