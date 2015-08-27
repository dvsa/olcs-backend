<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as Entity;

/**
 * LicenceVehicle Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class LicenceVehicleEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetActiveDiscWithout()
    {
        /** @var Entity $entity */
        $entity = $this->instantiate(Entity::class);

        $goodsDiscs = new ArrayCollection();

        $entity->setGoodsDiscs($goodsDiscs);

        $this->assertNull($entity->getActiveDisc());
    }

    public function testGetActiveDisc()
    {
        /** @var Entity $entity */
        $entity = $this->instantiate(Entity::class);

        /** @var GoodsDisc $goodsDisc */
        $goodsDisc = m::mock(GoodsDisc::class)->makePartial();

        $goodsDiscs = new ArrayCollection();
        $goodsDiscs->add($goodsDisc);

        $entity->setGoodsDiscs($goodsDiscs);

        $this->assertSame($goodsDisc, $entity->getActiveDisc());
    }

    public function testGetActiveDiscCeased()
    {
        /** @var Entity $entity */
        $entity = $this->instantiate(Entity::class);

        /** @var GoodsDisc $goodsDisc */
        $goodsDisc = m::mock(GoodsDisc::class)->makePartial();
        $goodsDisc->setCeasedDate(new DateTime());

        $goodsDiscs = new ArrayCollection();
        $goodsDiscs->add($goodsDisc);

        $entity->setGoodsDiscs($goodsDiscs);

        $this->assertNull($entity->getActiveDisc());
    }
}
