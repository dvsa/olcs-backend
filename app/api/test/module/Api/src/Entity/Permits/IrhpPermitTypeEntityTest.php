<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as Entity;
use Mockery as m;

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
        $this->sut = m::mock(Entity::class)->makePartial();
    }

    public function testGetCalculatedBundleValues()
    {
        $this->sut->shouldReceive('isEcmtAnnual')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('isEcmtShortTerm')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isBilateral')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isApplicationPathEnabled')
            ->once()
            ->withNoArgs()
            ->andReturn(false);

        $this->assertSame(
            [
                'isEcmtAnnual' => true,
                'isEcmtShortTerm' => false,
                'isEcmtRemoval' => false,
                'isBilateral' => false,
                'isMultilateral' => false,
                'isApplicationPathEnabled' => false,
            ],
            $this->sut->getCalculatedBundleValues()
        );
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
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsEcmtShortTerm
    */
    public function testIsEcmtShortTerm($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isEcmtShortTerm());
    }

    public function dpIsEcmtShortTerm()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, true],
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
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, true],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false],
        ];
    }

    /**
    * @dataProvider dpIsMultilateral
    */
    public function testIsMultilateral($id, $expected)
    {
        $this->sut->setId($id);

        $this->assertEquals($expected, $this->sut->isMultilateral());
    }

    public function dpIsMultilateral()
    {
        return [
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT, false],
            [Entity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false],
            [Entity::IRHP_PERMIT_TYPE_ID_BILATERAL, false],
            [Entity::IRHP_PERMIT_TYPE_ID_MULTILATERAL, true],
        ];
    }
}
