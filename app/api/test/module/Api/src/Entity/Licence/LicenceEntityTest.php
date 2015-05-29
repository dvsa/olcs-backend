<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;

/**
 * Licence Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class LicenceEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @dataProvider licenceTypeProvider
     */
    public function testCanBecomeSpecialRestricted($goodsOrPsv, $licenceType, $expected)
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();

        $licence->shouldReceive('getGoodsOrPsv->getId')
            ->andReturn($goodsOrPsv);

        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn($licenceType);

        $this->assertEquals($expected, $licence->canBecomeSpecialRestricted());
    }

    public function licenceTypeProvider()
    {
        return [
            [
                Entity::LICENCE_CATEGORY_GOODS_VEHICLE,
                Entity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            [
                Entity::LICENCE_CATEGORY_PSV,
                Entity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            [
                Entity::LICENCE_CATEGORY_PSV,
                Entity::LICENCE_TYPE_RESTRICTED,
                false
            ],
            [
                Entity::LICENCE_CATEGORY_PSV,
                Entity::LICENCE_TYPE_SPECIAL_RESTRICTED,
                true
            ]
        ];
    }
}
