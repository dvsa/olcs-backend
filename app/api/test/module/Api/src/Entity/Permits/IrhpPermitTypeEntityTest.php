<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as Entity;

/**
 * IrhpPermitType Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitTypeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Entity();
    }

    /**
    * @dataProvider dpIsEcmtAnnual
    */
    public function testIsEcmtAnnual($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isEcmtAnnual());
    }

    public function dpIsEcmtAnnual()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, true],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsBilateral
    */
    public function testIsBilateral($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isBilateral());
    }

    public function dpIsBilateral()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, true],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }
}
